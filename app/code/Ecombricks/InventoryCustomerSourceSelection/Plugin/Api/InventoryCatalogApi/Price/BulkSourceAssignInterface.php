<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Api\InventoryCatalogApi\Price;

/**
 * Bulk source assign interface price plugin
 */
class BulkSourceAssignInterface extends \Ecombricks\InventoryCommon\Plugin\Api\InventoryCatalogApi\BulkSourceAssignInterface
{
    
    /**
     * Construct
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\Price\Assign $assignSourceItemOptions
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Config $optionConfig
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\Price\Assign $assignSourceItemOptions,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Config $optionConfig
    )
    {
        parent::__construct($assignSourceItemOptions, $optionConfig);
    }
    
}