<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Setup\Patch\Data;

/**
 * Copy address data
 */
class CopyAddressData implements \Magento\Framework\Setup\Patch\DataPatchInterface
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
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->copyQuoteAddressShippingMethodData();
        $this->copyOrderShippingMethodData();
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

    /**
     * Copy quote address shipping method data
     * 
     * @return void
     */
    private function copyQuoteAddressShippingMethodData(): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->query(
            $connection->insertFromSelect(
                $connection->select()
                    ->from(
                        $this->moduleDataSetup->getTable('quote_address'),
                        [
                            'address_id',
                            'shipping_method',
                        ]
                    )
                    ->columns(
                        [
                            \Magento\InventoryApi\Api\Data\SourceInterface::SOURCE_CODE => new \Zend_Db_Expr($connection->quote($this->defaultSourceProvider->getCode())),
                        ]
                    )
                    ->where('shipping_method IS NOT NULL'),
                $this->moduleDataSetup->getTable('ecombricks_inventory__quote_address_source_shipping_method'),
                array_merge(
                    [
                        'address_id',
                        'value',
                    ],
                    [
                        \Magento\InventoryApi\Api\Data\SourceInterface::SOURCE_CODE
                    ]
                ),
                \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
            )
        );
    }
    
    /**
     * Copy order shipping method data
     * 
     * @return void
     */
    private function copyOrderShippingMethodData(): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->query(
            $connection->insertFromSelect(
                $connection->select()
                    ->from(
                        $this->moduleDataSetup->getTable('sales_order'),
                        [
                            'entity_id',
                            'shipping_method',
                        ]
                    )
                    ->columns(
                        [
                            \Magento\InventoryApi\Api\Data\SourceInterface::SOURCE_CODE => new \Zend_Db_Expr($connection->quote($this->defaultSourceProvider->getCode())),
                        ]
                    )
                    ->where('shipping_method IS NOT NULL'),
                $this->moduleDataSetup->getTable('ecombricks_inventory__sales_order_source_shipping_method'),
                array_merge(
                    [
                        'entity_id',
                        'value',
                    ],
                    [
                        \Magento\InventoryApi\Api\Data\SourceInterface::SOURCE_CODE
                    ]
                ),
                \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
            )
        );
    }
}