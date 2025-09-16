<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Setup\Patch\Data;

/**
 * Copy data
 */
class CopyData implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * Default source provider
     * 
     * @var \Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface
     */
    private $defaultSourceProvider;
    
    /**
     * Module data setup
     *
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * Construct
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     * @param \Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface $defaultSourceProvider
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
        \Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface $defaultSourceProvider
    )
    {
        $this->defaultSourceProvider = $defaultSourceProvider;
        $this->moduleDataSetup = $moduleDataSetup;
    }
    
    /**
     * Copy data
     * 
     * @param string $sourceTableName
     * @param string $tableName
     * @param array $columns
     * @param string|null $sourceCode
     * @return void
     */
    private function copyData(string $sourceTableName, string $tableName, array $columns, string $sourceCode = null): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        if (
            $connection->fetchOne(
                $connection->select()
                    ->from(
                        $this->moduleDataSetup->getTable($tableName),
                        [
                            'count' => new \Zend_Db_Expr('COUNT(*)'),
                        ]
                    )
            )
        ) {
            return;
        }
        $connection->query(
            $connection->insertFromSelect(
                $connection->select()
                    ->from(
                        $this->moduleDataSetup->getTable($sourceTableName),
                        $columns
                    )
                    ->columns(
                        [
                            \Magento\InventoryApi\Api\Data\SourceInterface::SOURCE_CODE => $sourceCode ? new \Zend_Db_Expr($connection->quote($sourceCode)) : new \Zend_Db_Expr('NULL'),
                        ]
                    ),
                $this->moduleDataSetup->getTable($tableName),
                array_merge($columns, [\Magento\InventoryApi\Api\Data\SourceInterface::SOURCE_CODE]),
                \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $sourceCode = $this->defaultSourceProvider->getCode();
        $this->copyData(
            'quote_item',
            'ecombricks_inventory__quote_item_source',
            [
                'item_id',
            ],
            $sourceCode
        );
        $this->copyData(
            'sales_order_item',
            'ecombricks_inventory__sales_order_item_source',
            [
                'item_id',
            ],
            $sourceCode
        );
        $this->copyData(
            'shipping_tablerate',
            'ecombricks_inventory__source_shipping_tablerate',
            [
                'website_id',
                'dest_country_id',
                'dest_region_id',
                'dest_zip',
                'condition_name',
                'condition_value',
                'price',
                'cost',
            ]
        );
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}