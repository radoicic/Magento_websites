<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Sales\AdminOrder;

/**
 * Create admin order plugin
 */
class Create extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Quote configuration
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config
     */
    private $quoteConfig;
    
    /**
     * Quote wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\QuoteFactory 
     */
    private $quoteWrapperFactory;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config $quoteConfig
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\QuoteFactory $quoteWrapperFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config $quoteConfig,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\QuoteFactory $quoteWrapperFactory
    )
    {
        parent::__construct($wrapperFactory);
        $this->quoteConfig = $quoteConfig;
        $this->quoteWrapperFactory = $quoteWrapperFactory;
    }
    
    /**
     * Around create order
     * 
     * @param \Magento\Sales\Model\AdminOrder\Create $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function aroundCreateOrder(
        \Magento\Sales\Model\AdminOrder\Create $subject,
        \Closure $proceed
    )
    {
        $this->setSubject($subject);
        if (!$this->quoteConfig->isSplitOrder()) {
            return $proceed();
        }
        $this->invokeSubjectMethod('_prepareCustomer');
        $this->invokeSubjectMethod('_validate');
        $this->invokeSubjectMethod('_prepareQuoteItems');
        $orderData = $this->getOrderData();
        $orders = $this->getSubjectPropertyValue('quoteManagement')->submit($subject->getQuote(), $orderData);
        if (count($orderData) > 0) {
            $oldOrder = $subject->getSession()->getOrder();
            if (count($orderData) == 1) {
                $newOrder = current($orders);
                $oldOrder->setRelationChildId($newOrder->getId());
                $oldOrder->setRelationChildRealId($newOrder->getIncrementId());
                $oldOrder->save();
            }
            $this->getSubjectPropertyValue('orderManagement')->cancel($oldOrder->getEntityId());
            foreach ($orders as $order) {
                $order->save();
            }
        }
        $this->sendOrdersEmails($orders);
        $eventManager = $this->getSubjectPropertyValue('_eventManager');
        foreach ($orders as $order) {
            $eventManager->dispatch(
                'checkout_submit_all_after',
                [
                    'order' => $order,
                    'quote' => $subject->getQuote(),
                ]
            );
        }
        return $orders;
    }

    /**
     * Get order data
     * 
     * @return array
     */
    protected function getOrderData(): array
    {
        $subject = $this->getSubject();
        $oldOrder = $subject->getSession()->getOrder();
        if (!$oldOrder->getId()) {
            return [];
        }
        $originalId = $oldOrder->getOriginalIncrementId();
        if (!$originalId) {
            $originalId = $oldOrder->getIncrementId();
        }
        $orderData = [];
        $index = 0;
        foreach ($this->quoteWrapperFactory->create($subject->getQuote())->getSourceCodes() as $sourceCode) {
            $orderData[$sourceCode] = [
                'original_increment_id' => $originalId,
                'relation_parent_id' => $oldOrder->getId(),
                'relation_parent_real_id' => $oldOrder->getIncrementId(),
                'edit_increment' => $oldOrder->getEditIncrement() + $index + 1,
                'increment_id' => $originalId.'-'.($oldOrder->getEditIncrement() + $index + 1)
            ];
            $index++;
        }
        return $orderData;
    }

    /**
     * Send orders emails
     * 
     * @return void
     */
    private function sendOrdersEmails($orders): void
    {
        $subject = $this->getSubject();
        if (!$subject->getSendConfirmation()) {
            return;
        }
        $emailSender = $this->getSubjectPropertyValue('emailSender');
        foreach ($orders as $order) {
            $emailSender->send($order);
        }
    }
}