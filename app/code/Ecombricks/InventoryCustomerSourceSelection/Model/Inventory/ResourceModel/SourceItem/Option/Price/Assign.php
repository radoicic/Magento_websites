<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\Price;

/**
 * Assign source item price options resource
 */
class Assign extends \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Assign
{
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider
     * @param \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface $getProductTypesBySkus
     * @param \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param string $tableName
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider,
        \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface $getProductTypesBySkus,
        \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        string $tableName = 'ecombricks_inventory__inventory_source_item_price'
    )
    {
        parent::__construct(
            $connectionProvider,
            $getProductTypesBySkus,
            $isSourceItemManagementAllowedForProductType,
            $tableName
        );
    }
    
}