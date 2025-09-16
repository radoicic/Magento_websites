<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Block\Checkout\Onepage;

/**
 * Checkout onepage success block wrapper
 */
class Success extends \Ecombricks\Common\DataObject\Wrapper implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * Quote configuration
     * 
     * @var \Ecombricks\Common\DataObject\WrapperFactory
     */
    private $quoteConfig;

    /**
     * Checkout session
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Checkout\Session
     */
    private $checkoutSession;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config $quoteConfig
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Checkout\Session $checkoutSession
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config $quoteConfig,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Checkout\Session $checkoutSession
    )
    {
        parent::__construct($objectReflectionFactory);
        $this->quoteConfig = $quoteConfig;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Check if is split order
     *
     * @return bool
     */
    public function isSplitOrder(): bool
    {
        return $this->quoteConfig->isSplitOrder();
    }

    /**
     * Get orders
     * 
     * @return array
     */
    public function getOrders(): array
    {
        return $this->checkoutSession->getLastRealOrders();
    }

    /**
     * Get order
     * 
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder(): \Magento\Sales\Api\Data\OrderInterface
    {
        return $this->checkoutSession->getLastRealOrder();
    }
    
    /**
     * Get view order URL
     * 
     * @param \Magento\Sales\Model\Order|null $order
     * @return string
     */
    public function getViewOrderUrl(\Magento\Sales\Model\Order $order = null): string
    {
        if ($order === null) {
            $order = $this->getOrder();
        }
        return $this->getObject()->getUrl(
            'sales/order/view',
            [
                'order_id' => $order->getId(),
            ]
        );
    }
    
    /**
     * Get print order URL
     * 
     * @param \Magento\Sales\Model\Order|null $order
     * @return string
     */
    public function getPrintOrderUrl(\Magento\Sales\Model\Order $order = null): string
    {
        if ($order === null) {
            $order = $this->getOrder();
        }
        return $this->getObject()->getUrl(
            'sales/order/print',
            [
                'order_id' => $order->getId(),
            ]
        );
    }

    /**
     * Get can print order
     * 
     * @param \Magento\Sales\Model\Order|null $order
     * @return bool
     */
    public function getCanPrintOrder(\Magento\Sales\Model\Order $order = null): bool
    {
        if ($order === null) {
            $order = $this->getOrder();
        }
        return $this->invokeMethod('isVisible', $order);
    }
    
    /**
     * Get can print order
     * 
     * @param \Magento\Sales\Model\Order|null $order
     * @return bool
     */
    public function getCanViewOrder(\Magento\Sales\Model\Order $order = null): bool
    {
        if ($order === null) {
            $order = $this->getOrder();
        }
        return $this->invokeMethod('canViewOrder', $order);
    }

    /**
     * Get order ID
     * 
     * @param \Magento\Sales\Model\Order|null $order
     * @return string
     */
    public function getOrderId(\Magento\Sales\Model\Order $order = null): string
    {
        if ($order === null) {
            $order = $this->getOrder();
        }
        return $order->getIncrementId();
    }
}