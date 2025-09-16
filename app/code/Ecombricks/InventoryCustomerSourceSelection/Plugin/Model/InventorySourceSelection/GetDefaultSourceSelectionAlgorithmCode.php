<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventorySourceSelection;

/**
 * Get default source selection algorithm code plugin
 */
class GetDefaultSourceSelectionAlgorithmCode
{
    /**
     * Around execute
     * 
     * @param \Magento\InventorySourceSelection\Model\GetDefaultSourceSelectionAlgorithmCode
     * @param \Closure $proceed
     * @return string
     */
    public function aroundExecute(
        \Magento\InventorySourceSelection\Model\GetDefaultSourceSelectionAlgorithmCode $subject,
        \Closure $proceed
    ) : string
    {
        return 'default';
    }
}