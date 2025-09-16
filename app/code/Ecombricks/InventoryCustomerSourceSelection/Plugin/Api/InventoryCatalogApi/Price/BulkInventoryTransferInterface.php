<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Api\InventoryCatalogApi\Price;

/**
 * Bulk inventory transfer interface price plugin
 */
class BulkInventoryTransferInterface extends \Ecombricks\InventoryCommon\Plugin\Api\InventoryCatalogApi\BulkInventoryTransferInterface
{
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\Price\Transfer $transferSourceItemOptions
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\Price\Unassign $unassignSourceItemOptions
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Config $optionConfig
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\Price\Transfer $transferSourceItemOptions,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\Price\Unassign $unassignSourceItemOptions,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Config $optionConfig
    )
    {
        parent::__construct($transferSourceItemOptions, $unassignSourceItemOptions, $optionConfig);
    }
    
}