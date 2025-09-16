<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Helper\Paypal;

/**
 * PayPal checkout helper plugin
 */
class Checkout
{
    /**
     * Session
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Checkout\Session
     */
    private $session;

    /**
     * Quote configuration
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config
     */
    private $quoteConfig;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Checkout\Session $session
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config $quoteConfig
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\Checkout\Session $session,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config $quoteConfig
    )
    {
        $this->session = $session;
        $this->quoteConfig = $quoteConfig;
    }
    
    /**
     * Around cancel current order
     *
     * @param \Magento\Paypal\Helper\Checkout $subject
     * @param callable $proceed
     * @param string $comment
     * @return bool
     */
    public function aroundCancelCurrentOrder(
        \Magento\Paypal\Helper\Checkout $subject,
        \Closure $proceed,
        $comment
    )
    {
        if (!$this->quoteConfig->isSplitOrder()) {
            return $proceed($comment);
        }
        $canceled = false;
        $orders = $this->session->getLastRealOrders();
        if (empty($orders)) {
            return $canceled;
        }
        foreach ($orders as $order) {
            if (!$order->getId() || $order->getState() == \Magento\Sales\Model\Order::STATE_CANCELED) {
                continue;
            }
            $order->registerCancellation($comment)->save();
            $canceled = true;
        }
        return $canceled;
    }
}