<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Api\InventoryCatalogApi\TaxClass;

/**
 * Bulk inventory transfer interface tax class plugin
 */
class BulkInventoryTransferInterface extends \Ecombricks\InventoryCommon\Plugin\Api\InventoryCatalogApi\BulkInventoryTransferInterface
{
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\TaxClass\Transfer $transferSourceItemOptions
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\TaxClass\Unassign $unassignSourceItemOptions
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Config $optionConfig
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\TaxClass\Transfer $transferSourceItemOptions,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\TaxClass\Unassign $unassignSourceItemOptions,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Config $optionConfig
    )
    {
        parent::__construct($transferSourceItemOptions, $unassignSourceItemOptions, $optionConfig);
    }
    
}