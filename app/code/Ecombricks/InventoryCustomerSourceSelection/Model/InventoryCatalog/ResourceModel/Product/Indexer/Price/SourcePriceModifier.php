<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventoryCatalog\ResourceModel\Product\Indexer\Price;

/**
 * Source product price indexer modifier
 */
class SourcePriceModifier implements \Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\PriceModifierInterface
{
    /**
     * Connection provider
     * 
     * @var \Ecombricks\Common\Model\ResourceModel\ConnectionProvider 
     */
    private $connectionProvider;

    /**
     * Table strategy
     * 
     * @var \Magento\Framework\Indexer\Table\StrategyInterface 
     */
    private $tableStrategy;

    /**
     * Column value expression factory
     * 
     * @var \Magento\Framework\DB\Sql\ColumnValueExpressionFactory
     */
    private $columnValueExpressionFactory;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider
     * @param \Magento\Framework\Indexer\Table\StrategyInterface $tableStrategy
     * @param \Magento\Framework\DB\Sql\ColumnValueExpressionFactory $columnValueExpressionFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider,
        \Magento\Framework\Indexer\Table\StrategyInterface $tableStrategy,
        \Magento\Framework\DB\Sql\ColumnValueExpressionFactory $columnValueExpressionFactory
    )
    {
        $this->connectionProvider = $connectionProvider;
        $this->tableStrategy = $tableStrategy;
        $this->columnValueExpressionFactory = $columnValueExpressionFactory;
    }

    /**
     * Modify price
     * 
     * @param \Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\IndexTableStructure $priceTable
     * @param array $entityIds
     * @return void
     */
    public function modifyPrice(\Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\IndexTableStructure $priceTable, array $entityIds = []) : void
    {
        $connection = $this->connectionProvider->getConnection();
        $this->prepareProductIndexPriceSourceTable();
        $connection->query($this->getProductIndexPriceSourceSql($priceTable));
        $connection->query($this->getUpdateSql($priceTable));
        $this->prepareProductIndexPriceSourceTable();
    }

    /**
     * Get product index price source table
     * 
     * @return string
     */
    private function getProductIndexPriceSourceTable(): string
    {
        return $this->tableStrategy->getTableName('ecombricks_inventory__catalog_product_index_price_source');
    }

    /**
     * Prepare product index price source table
     * 
     * @return void
     */
    private function prepareProductIndexPriceSourceTable(): void
    {
        $this->connectionProvider->getConnection()->delete($this->getProductIndexPriceSourceTable());
    }

    /**
     * Get product index price source select
     * 
     * @param \Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\IndexTableStructure $priceTable
     * @return \Magento\Framework\DB\Select
     */
    private function getProductIndexPriceSourceSelect(\Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\IndexTableStructure $priceTable): \Magento\Framework\DB\Select
    {
        $connection = $this->connectionProvider->getConnection();
        $select = $this->connectionProvider->getSelect()
            ->from(
                ['i' => $priceTable->getTableName()],
                ['entity_id', 'website_id']
            )
            ->join(
                ['e' => $this->connectionProvider->getTable('catalog_product_entity')],
                'e.entity_id = i.'.$priceTable->getEntityField(),
                []
            )
            ->join(
                ['cwd' => $this->connectionProvider->getTable('catalog_product_index_website')],
                'cwd.website_id = i.'.$priceTable->getWebsiteField(),
                []
            )
            ->join(
                ['w' => $this->connectionProvider->getTable('store_website')],
                'w.website_id = cwd.website_id',
                []
            )
            ->join(
                ['ssc' => $this->connectionProvider->getTable('inventory_stock_sales_channel')],
                $this->connectionProvider->getCondition([
                    'ssc.type = \''.\Magento\InventorySalesApi\Api\Data\SalesChannelInterface::TYPE_WEBSITE.'\'',
                    'ssc.code = w.code',
                ], 'AND'),
                []
            )  
            ->join(
                ['ssl' => $this->connectionProvider->getTable('inventory_source_stock_link')],
                'ssl.stock_id = ssc.stock_id',
                []
            )
            ->join(
                ['sip' => $this->connectionProvider->getTable('ecombricks_inventory__inventory_source_item_price')],
                $this->connectionProvider->getCondition([
                    'sip.sku = e.sku',
                    'sip.source_code = ssl.source_code',
                ], 'AND'),
                []
            )
            ->where('sip.value IS NOT NULL')
            ->group([
                'i.'.$priceTable->getEntityField(),
                'i.'.$priceTable->getWebsiteField()
            ])
            ->columns([
                $priceTable->getMinPriceField() => $connection->getLeastSql(['i.'.$priceTable->getMinPriceField(), 'MIN(sip.value)']),
                $priceTable->getMaxPriceField() => $connection->getGreatestSql(['i.'.$priceTable->getMaxPriceField(), 'MAX(sip.value)']),
            ]);
        return $select;
    }
    
    /**
     * Get product index price source SQL
     * 
     * @param \Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\IndexTableStructure $priceTable
     * @return string
     */
    private function getProductIndexPriceSourceSql(\Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\IndexTableStructure $priceTable): string
    {
        return $this->getProductIndexPriceSourceSelect($priceTable)
            ->insertFromSelect($this->getProductIndexPriceSourceTable());
    }

    /**
     * Create column value expression
     * 
     * @param string $expression
     * @return \Magento\Framework\DB\Sql\ColumnValueExpression
     */
    private function createColumnValueExpression(string $expression): \Magento\Framework\DB\Sql\ColumnValueExpression
    {
        return $this->columnValueExpressionFactory->create(['expression' => $expression]);
    }

    /**
     * Get update select
     * 
     * @param \Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\IndexTableStructure $priceTable
     * @return \Magento\Framework\DB\Select
     */
    private function getUpdateSelect(\Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\IndexTableStructure $priceTable): \Magento\Framework\DB\Select
    {
        $select = $this->connectionProvider->getSelect();
        $select->join(
            ['pips' => $this->getProductIndexPriceSourceTable()],
            $this->connectionProvider->getCondition([
                'i.'.$priceTable->getEntityField().' = pips.entity_id',
                'i.'.$priceTable->getWebsiteField().' = pips.website_id',
            ], 'AND'),
            []
        );
        $select->columns([
            $priceTable->getMinPriceField() => $this->createColumnValueExpression('pips.min_price'),
            $priceTable->getMaxPriceField() => $this->createColumnValueExpression('pips.max_price'),
        ]);
        return $select;
    }
    
    /**
     * Get update SQL
     * 
     * @param \Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\IndexTableStructure $priceTable
     * @return string
     */
    private function getUpdateSql(\Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\IndexTableStructure $priceTable): string
    {
        return $this->getUpdateSelect($priceTable)
            ->crossUpdateFromSelect(['i' => $priceTable->getTableName()]);
    }
}