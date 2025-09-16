<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Controller\Paypal\Express\AbstractExpress;

/**
 * Cancel PayPal express controller plugin
 */
class Cancel extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Quote configuration
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config
     */
    private $quoteConfig;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config $quoteConfig
     * @retun void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config $quoteConfig
    )
    {
        parent::__construct($wrapperFactory);
        $this->quoteConfig = $quoteConfig;
    }
    
    /**
     * Around execute
     * 
     * @param \Magento\Paypal\Controller\Express\AbstractExpress\Cancel $subject
     * @param callable $proceed
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundExecute(
        \Magento\Paypal\Controller\Express\AbstractExpress\Cancel $subject,
        \Closure $proceed
    )
    {
        $this->setSubject($subject);
        if (!$this->quoteConfig->isSplitOrder()) {
            return $proceed();
        }
        $messageManager = $this->getSubjectPropertyValue('messageManager');
        try {
            $this->invokeSubjectMethod('_initToken', false);
            foreach ($this->getLastOrders() as $order) {
                $this->cancelOrder($order);
            }
            $this->clearCheckoutSession($this->getSubjectPropertyValue('_checkoutSession'));
            $this->invokeSubjectMethod('_getSession')->unsQuoteId(); 
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $messageManager->addExceptionMessage($exception, $exception->getMessage());
        } catch (\Exception $exception) {
            $messageManager->addExceptionMessage($exception, __('Unable to cancel Express Checkout'));
        }
        return $this->getSubjectPropertyValue('resultFactory')->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT)
            ->setPath('checkout/cart');
    }

    /**
     * Get last orders
     * 
     * @return array
     */
    private function getLastOrders(): array
    {
        $orders = [];
        $orderIds = $this->getSubjectPropertyValue('_checkoutSession')->getLastOrderIds();
        if (empty($orderIds)) {
            return $orders;
        }
        $orderFactory = $this->getSubjectPropertyValue('_orderFactory');
        foreach ($orderIds as $orderId) {
            $orders[] = $orderFactory->create()->load($orderId);
        }
        return $orders;
    }

    /**
     * Cancel order
     * 
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     */
    private function cancelOrder(\Magento\Sales\Api\Data\OrderInterface $order): void
    {
        $messageManager = $this->getSubjectPropertyValue('messageManager');
        if (!$order->getId() || $order->getQuoteId() != $this->getSubjectPropertyValue('_checkoutSession')->getQuoteId()) {
            $messageManager->addSuccessMessage(__('Express Checkout has been canceled.'));
            return;
        }
        $order->cancel()->save();
        $messageManager->addSuccessMessage(__('Express Checkout and Order have been canceled.'));
    }

    /**
     * Clear checkout session
     * 
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @return void
     */
    private function clearCheckoutSession(\Magento\Checkout\Model\Session $checkoutSession): void
    {
        $checkoutSession->unsLastQuoteId();
        $checkoutSession->unsLastSuccessQuoteId();
        $checkoutSession->unsLastOrderId();
        $checkoutSession->unsLastRealOrderId();
        $checkoutSession->unsLastOrderStatus();
        $checkoutSession->unsLastOrderIds();
        $checkoutSession->unsLastRealOrderIds();
        $checkoutSession->unsLastOrderStatuses();
    }
}