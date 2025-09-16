<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Checkout;

/**
 * Checkout session
 */
class Session extends \Magento\Checkout\Model\Session
{
    /**
     * Orders
     *
     * @var \Magento\Sales\Model\Order
     */
    private $orders;

    /**
     * Get last real orders
     *
     * @return array
     */
    public function getLastRealOrders(): array
    {
        $orders = [];
        $orderIds = $this->getLastRealOrderIds();
        if (empty($orderIds)) {
            return $orders;
        }
        foreach ($orderIds as $orderId) {
            if (isset($this->orders[$orderId])) {
                $orders[$orderId] = $this->orders[$orderId];
                continue;
            }
            $order = $this->_orderFactory->create();
            $order->loadByIncrementId($orderId);
            $this->orders[$orderId] = $order;
            $orders[$orderId] = $order;
        }
        return $orders;
    }
}