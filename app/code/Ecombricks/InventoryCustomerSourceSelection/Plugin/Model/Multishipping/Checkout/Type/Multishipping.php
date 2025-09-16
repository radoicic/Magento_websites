<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Multishipping\Checkout\Type;

/**
 * Multishipping checkout type model plugin
 */
class Multishipping extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Quote configuration
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config
     */
    private $quoteConfig;
    
    /**
     * Quote address wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory
     */
    private $quoteAddressWrapperFactory;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config $quoteConfig
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config $quoteConfig,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory
    )
    {
        parent::__construct($wrapperFactory);
        $this->quoteConfig = $quoteConfig;
        $this->quoteAddressWrapperFactory = $quoteAddressWrapperFactory;
    }

    /**
     * Around create orders
     * 
     * @param \Magento\Multishipping\Model\Checkout\Type\Multishipping $subject
     * @param \Closure $proceed
     * @return \Magento\Multishipping\Model\Checkout\Type\Multishipping
     * @throws \Exception
     */
    public function aroundCreateOrders(
        \Magento\Multishipping\Model\Checkout\Type\Multishipping $subject,
        \Closure $proceed
    )
    {
        $this->setSubject($subject);
        if (!$this->quoteConfig->isSplitOrder()) {
            return $proceed();
        }
        $this->invokeSubjectMethod('_validate');
        $quote = $subject->getQuote();
        $orders = [];
        $session = $this->getSubjectPropertyValue('_session');
        $eventManager = $this->getSubjectPropertyValue('_eventManager');
        try {
            $orders = $this->prepareOrders($this->getQuoteAddresses());
            $exceptions = $this->getSubjectPropertyValue('placeOrderFactory')->create($quote->getPayment()->getMethod())->place($orders);
            $this->logExceptions($exceptions);
            $placedOrders = $this->getPlacedOrders($orders, $exceptions);
            $this->sendOrderEmails($placedOrders);
            $addressErrors = [];
            $failedOrders = $this->getFailedOrders($orders, $exceptions);
            if (!empty($failedOrders)) {
                $this->removeQuoteItems($this->getOrdersQuoteItemIds($placedOrders));
                $addressErrors = $this->getQuoteAddressErrors($failedOrders, $exceptions);
            } else {
                $this->getSubjectPropertyValue('_checkoutSession')->setLastQuoteId($quote->getId());
                $quote->setIsActive(false);
                $this->getSubjectPropertyValue('quoteRepository')->save($quote);
            }
            $session->setOrderIds($this->getOrderIncrementIds($placedOrders));
            $session->setAddressErrors($addressErrors);
            $eventManager->dispatch(
                'checkout_submit_all_after',
                [
                    'orders' => $orders,
                    'quote' => $quote,
                ]
            );
        } catch (\Exception $exception) {
            $eventManager->dispatch(
                'checkout_multishipping_refund_all',
                [
                    'orders' => $orders,
                ]
            );
            throw $exception;
        }
        return $subject;
    }

    /**
     * Get quote addresses
     * 
     * @return array
     */
    protected function getQuoteAddresses(): array
    {
        $subject = $this->getSubject();
        $quoteAddresses = [];
        $quote = $subject->getQuote();
        foreach ($quote->getAllShippingAddresses() as $quoteAddress) {
            $quoteAddressWrapper = $this->quoteAddressWrapperFactory->create($quoteAddress);
            foreach ($quoteAddressWrapper->getSourceCodes() as $sourceCode) {
                $quoteAddresses[] = $quoteAddressWrapper->createSourceClone((string) $sourceCode);
            }
        }
        if ($quote->hasVirtualItems()) {
            $quoteAddress = $quote->getBillingAddress();
            $quoteAddressWrapper = $this->quoteAddressWrapperFactory->create($quoteAddress);
            foreach ($quoteAddressWrapper->getSourceCodes() as $sourceCode) {
                $quoteAddresses[] = $quoteAddressWrapper->createSourceClone((string) $sourceCode);
            }
        }
        return $quoteAddresses;
    }
    
    /**
     * Prepare orders
     * 
     * @param array $quoteAddresses
     * @return array
     */
    private function prepareOrders($quoteAddresses): array
    {
        $subject = $this->getSubject();
        $orders = [];
        $quote = $subject->getQuote();
        $eventManager = $this->getSubjectPropertyValue('_eventManager');
        foreach ($quoteAddresses as $quoteAddress) {
            $order = $this->invokeSubjectMethod('_prepareOrder', $quoteAddress);
            $orders[] = $order;
            $eventManager->dispatch(
                'checkout_type_multishipping_create_orders_single',
                [
                    'order' => $order,
                    'address' => $quoteAddress,
                    'quote' => $quote,
                ]
            );
        }
        return $orders;
    }
    
    /**
     * Logs exceptions
     *
     * @param array $exceptions
     * @return \Magento\Multishipping\Model\Checkout\Type\Multishipping
     */
    private function logExceptions($exceptions): void
    {
        $logger = $this->getSubjectPropertyValue('logger');
        foreach ($exceptions as $exception) {
            $logger->critical($exception);
        }
    }
    
    /**
     * Get placed orders
     * 
     * @param array $orders
     * @param array $exceptions
     * @return array
     */
    private function getPlacedOrders($orders, $exceptions): array
    {
        $placedOrders = [];
        foreach ($orders as $order) {
            if (!isset($exceptions[$order->getIncrementId()])) {
                $placedOrders[] = $order;
            }
        }
        return $placedOrders;
    }
    
    /**
     * Get failed orders
     * 
     * @param array $orders
     * @param array $exceptions
     * @return array
     */
    private function getFailedOrders($orders, $exceptions): array
    {
        $failedOrders = [];
        foreach ($orders as $order) {
            if (isset($exceptions[$order->getIncrementId()])) {
                $failedOrders[] = $order;
            }
        }
        return $failedOrders;
    }
    
    /**
     * Send order emails
     * 
     * @param array $orders
     * @return \Magento\Multishipping\Model\Checkout\Type\Multishipping
     */
    private function sendOrderEmails($orders): void
    {
        $orderSender = $this->getSubjectPropertyValue('orderSender');
        foreach ($orders as $order) {
            if ($order->getCanSendNewEmailFlag()) {
                $orderSender->send($order);
            }
        }
    }

    /**
     * Get order increment IDs
     * 
     * @param array $orders
     * @return array
     */
    private function getOrderIncrementIds(array $orders): array
    {
        $orderIncrementIds = [];
        foreach ($orders as $order) {
            $orderIncrementIds[$order->getId()] = $order->getIncrementId();
        }
        return $orderIncrementIds;
    }

    /**
     * Get order quote item IDs
     * 
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return array
     */
    private function getOrderQuoteItemIds(\Magento\Sales\Api\Data\OrderInterface $order): array
    {
        $quoteItemIds = [];
        foreach ($order->getItems() as $orderItem) {
            $quoteItemIds[] = $orderItem->getQuoteItemId();
        }
        return $quoteItemIds;
    }

    /**
     * Get orders quote item IDs
     * 
     * @param array $orders
     * @return array
     */
    private function getOrdersQuoteItemIds(array $orders): array
    {
        $orderQuoteItemIds = [];
        foreach ($orders as $order) {
            $orderQuoteItemIds = array_merge($orderQuoteItemIds, $this->getOrderQuoteItemIds($order));
        }
        return $orderQuoteItemIds;
    }
    
    /**
     * Get order quote address ID
     * 
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return int
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    private function getOrderQuoteAddressId(\Magento\Sales\Api\Data\OrderInterface $order): int
    {
        $subject = $this->getSubject();
        $orderItems = $order->getItems();
        $orderItem = array_pop($orderItems);
        foreach ($subject->getQuote()->getAllAddresses() as $quoteAddress) {
            foreach ($quoteAddress->getAllItems() as $quoteAddressItem) {
                if ($quoteAddressItem->getId() != $orderItem->getQuoteItemId()) {
                    continue;
                }
                return (int) $quoteAddress->getId();
            }
        }
        throw new \Magento\Framework\Exception\NotFoundException(__('Quote address for failed order ID "%1" not found.', $order->getEntityId()));
    }
    
    /**
     * Decrease quote item qty
     *
     * @param int $quoteItemId
     * @param float $qty
     * @return void
     */
    private function decreaseQuoteItemQty(int $quoteItemId, float $qty): void
    {
        $subject = $this->getSubject();
        $quote = $subject->getQuote();
        $quoteItem = $quote->getItemById($quoteItemId);
        if (empty($quoteItem)) {
            return;
        }
        $decreasedQty = $quoteItem->getQty() - $qty;
        if ($decreasedQty > 0) {
            $quoteItem->setQty($decreasedQty);
        } else {
            $quote->removeItem($quoteItem->getId());
            $quote->setIsMultiShipping(1);
        }
    }
    
    /**
     * Remove quote items
     * 
     * @param array $quoteAdressItemIds
     * @return void
     */
    private function removeQuoteItems(array $quoteAdressItemIds): void
    {
        $subject = $this->getSubject();
        foreach ($subject->getQuote()->getAllAddresses() as $quoteAddress) {
            foreach ($quoteAddress->getAllItems() as $quoteAddressItem) {
                if (in_array($quoteAddressItem->getId(), $quoteAdressItemIds)) {
                    continue;
                }
                if ($quoteAddressItem->getProduct()->getIsVirtual()) {
                    $quoteAddressItem->isDeleted(true);
                } else {
                    $quoteAddress->isDeleted(true);
                }
                $this->decreaseQuoteItemQty((int) $quoteAddressItem->getQuoteItemId(), (float) $quoteAddressItem->getQty());
            }
        }
        $this->save();
    }
    
    /**
     * Get quote address errors
     * 
     * @param array $orders
     * @param array $exceptions
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    private function getQuoteAddressErrors(array $orders, array $exceptions)
    {
        $errors = [];
        foreach ($orders as $order) {
            if (!isset($exceptions[$order->getIncrementId()])) {
                throw new \Magento\Framework\Exception\NotFoundException(__('Exception for failed order not found.'));
            }
            $quoteAddressId = $this->getOrderQuoteAddressId($order);
            $errors[$quoteAddressId] = $exceptions[$order->getIncrementId()]->getMessage();
        }
        return $errors;
    }
}