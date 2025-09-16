<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\Price;

/**
 * Get source item price options resource
 */
class Get extends \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Get
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
        string $tableName = 'ecombricks_inventory__inventory_source_item_price'
    )
    {
        parent::__construct($connectionProvider, $tableName);
    }
    
}