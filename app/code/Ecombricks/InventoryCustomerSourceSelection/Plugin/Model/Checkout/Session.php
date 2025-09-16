<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Checkout;

/**
 * Checkout session plugin
 */
class Session
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
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config $quoteConfig
     * @retun void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config $quoteConfig
    )
    {
        $this->quoteConfig = $quoteConfig;
    }
    
    /**
     * Around clear helper data
     * 
     * @param \Magento\Checkout\Model\Session $subject
     * @param \Closure $proceed
     * @return void
     */
    public function aroundClearHelperData(
        \Magento\Checkout\Model\Session $subject,
        \Closure $proceed
    )
    {
        $proceed();
        if (!$this->quoteConfig->isSplitOrder()) {
            return;
        }
        $subject
            ->setLastOrderIds(null)
            ->setLastRealOrderIds(null);
    }
}