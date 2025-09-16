<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Api\InventoryCatalogApi\Price;

/**
 * Bulk source unassign interface price plugin
 */
class BulkSourceUnassignInterface extends \Ecombricks\InventoryCommon\Plugin\Api\InventoryCatalogApi\BulkSourceUnassignInterface
{
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\Price\Unassign $unassignSourceItemOptions
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Config $optionConfig
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\Price\Unassign $unassignSourceItemOptions,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Config $optionConfig
    )
    {
        parent::__construct($unassignSourceItemOptions, $optionConfig);
    }
    
}