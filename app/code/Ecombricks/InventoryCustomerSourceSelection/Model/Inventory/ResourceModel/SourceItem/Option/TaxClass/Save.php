<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\TaxClass;

/**
 * Save source item tax class options resource
 */
class Save extends \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Save
{
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider
     * @param string $tableName
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider,
        string $tableName = 'ecombricks_inventory__inventory_source_item_tax_class'
    )
    {
        parent::__construct($connectionProvider, $tableName);
    }
    
}