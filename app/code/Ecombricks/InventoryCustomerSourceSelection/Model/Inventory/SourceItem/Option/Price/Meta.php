<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price;

/**
 * Source item price option meta
 */
class Meta extends \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta
{
    /**
     * Constructor
     * 
     * @param string $name
     * @param string $label
     * @param string $tableName
     * @param string $backendType
     * @return void
     */
    public function __construct(
        string $name = 'price',
        string $label = 'Price',
        string $tableName = 'ecombricks_inventory__inventory_source_item_price',
        string $backendType = 'decimal'
    )
    {
        parent::__construct(
            $name,
            $label,
            $tableName,
            $backendType
        );
    }
}