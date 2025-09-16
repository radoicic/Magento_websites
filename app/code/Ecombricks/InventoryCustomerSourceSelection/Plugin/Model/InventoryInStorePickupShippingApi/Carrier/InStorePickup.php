<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventoryInStorePickupShippingApi\Carrier;

/**
 * In store pickup carrier plugin
 */
class InStorePickup
{
    /**
     * Around is active
     * 
     * @param \Magento\InventoryInStorePickupShippingApi\Model\Carrier\InStorePickup $subject
     * @param callable $proceed
     * @return bool
     */
    public function aroundIsActive(
        \Magento\InventoryInStorePickupShippingApi\Model\Carrier\InStorePickup $subject,
        callable $proceed
    )
    {
        return false;
    }
}