<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Block\Sales\Adminhtml\Order\Create\Shipping\Method;

/**
 * Create order shipping method form plugin
 */
class Form extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Stock state
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Api\CatalogInventory\StockStateInterface
     */
    private $stockState;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Api\CatalogInventory\StockStateInterface $stockState
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Api\CatalogInventory\StockStateInterface $stockState
    )
    {
        $this->stockState = $stockState;
    }

    /**
     * Around get active method rate
     * 
     * @param \Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Method\Form $subject
     * @param \Closure $proceed
     */
    public function aroundGetActiveMethodRate(
        \Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Method\Form $subject,
        \Closure $proceed
    )
    {
        $groupedRates = $subject->getShippingRates();
        if (empty($groupedRates)) {
            return [];
        }
        return $subject->getCurrentShippingRate();
    }
}