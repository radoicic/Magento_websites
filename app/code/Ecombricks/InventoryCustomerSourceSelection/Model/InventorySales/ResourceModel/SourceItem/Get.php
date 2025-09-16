<?php

/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\ResourceModel\SourceItem;

/**
 * Get source items resource
 */
class Get
{
    /**
     * Connection provider
     *
     * @var \Ecombricks\Common\Model\ResourceModel\ConnectionProvider
     */
    private $connectionProvider;

    /**
     * Get is stock item salable condition
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $getIsStockItemSalableCondition;

    /**
     * Constructor
     *
     * @param \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider
     * @param \Magento\InventorySales\Model\ResourceModel\IsStockItemSalableCondition\GetIsStockItemSalableConditionInterface $getIsStockItemSalableCondition
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider,
        \Magento\InventorySales\Model\ResourceModel\IsStockItemSalableCondition\GetIsStockItemSalableConditionInterface $getIsStockItemSalableCondition
    ) {
        $this->connectionProvider = $connectionProvider;
        $this->getIsStockItemSalableCondition = $getIsStockItemSalableCondition;
    }

    /**
     * Execute
     *
     * @param array $skus
     * @param int $stockId
     * @return array
     */
    public function execute(array $skus, int $stockId): array
    {
        $sourceItemTableAlias = 'source_item';
        $stockSourceLinkTableAlias = 'stock_source_link';
        $sourceItemSku = $sourceItemTableAlias . '.' . \Magento\InventoryApi\Api\Data\SourceItemInterface::SKU;
        $sourceItemSourceCode = $sourceItemTableAlias . '.' . \Magento\InventoryApi\Api\Data\SourceItemInterface::SOURCE_CODE;
        $stockSourceLinkStockId = $stockSourceLinkTableAlias . '.' . \Magento\InventoryApi\Api\Data\StockSourceLinkInterface::STOCK_ID;
        $stockSourceLinkSourceCode = $stockSourceLinkTableAlias . '.' . \Magento\InventoryApi\Api\Data\StockSourceLinkInterface::SOURCE_CODE;
        $stockSourceLinkPriority = $stockSourceLinkTableAlias . '.' . \Magento\InventoryApi\Api\Data\StockSourceLinkInterface::PRIORITY;
        $select = $this->getIsSalableConditionSelect($sourceItemTableAlias, $skus, $stockId);
        $select
            ->from(
                [$sourceItemTableAlias => $this->connectionProvider->getTable(\Magento\Inventory\Model\ResourceModel\SourceItem::TABLE_NAME_SOURCE_ITEM)],
                []
            )
            ->from(
                [$stockSourceLinkTableAlias => $this->connectionProvider->getTable(\Magento\Inventory\Model\ResourceModel\StockSourceLink::TABLE_NAME_STOCK_SOURCE_LINK)],
                []
            )
            ->columns([
                'sku' => $sourceItemSku,
                'source_code' => $sourceItemSourceCode,
                'quantity' => $this->getQuantityExpression($sourceItemTableAlias),
                'is_salable' => $this->getIsSalableExpression($select),
            ])
            ->where($sourceItemSku . ' IN (?)', $skus)
            ->where($stockSourceLinkStockId . ' = ?', $stockId)
            ->where($stockSourceLinkSourceCode . ' = ' . $sourceItemSourceCode)
            ->order([$stockSourceLinkPriority . ' ' . \Magento\Framework\DB\Select::SQL_ASC])
            ->group([$sourceItemSku, $sourceItemSourceCode]);

        return $this->connectionProvider->getConnection()->fetchAll($select);
    }

    /**
     * Get quantity expression
     *
     * @param string $sourceItemTableAlias
     * @return string
     */
    private function getQuantityExpression(string $sourceItemTableAlias): string
    {
        return 'SUM(' . $this->connectionProvider->getConnection()->getCheckSql(
                $sourceItemTableAlias . '.' . \Magento\InventoryApi\Api\Data\SourceItemInterface::STATUS . ' = ' . \Magento\InventoryApi\Api\Data\SourceItemInterface::STATUS_OUT_OF_STOCK,
                0,
                $sourceItemTableAlias . '.' . \Magento\InventoryApi\Api\Data\SourceItemInterface::QUANTITY
            ) . ')';
    }

    /**
     * Get is salable condition select
     *
     * @param string $sourceItemTableAlias
     * @param string[] skus
     * @param int $stockId
     * @return \Magento\Framework\DB\Select
     */
    private function getIsSalableConditionSelect(
        string $sourceItemTableAlias,
        array $skus,
        int $stockId
    ): \Magento\Framework\DB\Select {
        return $this->connectionProvider->getSelect()
            ->joinLeft(
                ['product' => $this->connectionProvider->getTable('catalog_product_entity')],
                'product.sku = ' . $sourceItemTableAlias . '.' . \Magento\InventoryApi\Api\Data\SourceItemInterface::SKU,
                []
            )
            ->joinLeft(
                ['legacy_stock_item' => $this->connectionProvider->getTable('cataloginventory_stock_item')],
                'product.entity_id = legacy_stock_item.product_id',
                []
            )
            ->joinLeft(
                ['reservations' => $this->getReservationJoinTable($skus, $stockId)],
                'reservations.sku = ' . $sourceItemTableAlias . '.' . \Magento\InventoryApi\Api\Data\SourceItemInterface::SKU,
                []
            );
    }

    /**
     * @param array $skus
     * @param int $stockId
     * @return \Magento\Framework\DB\Select
     */
    private function getReservationJoinTable(array $skus, int $stockId): \Magento\Framework\DB\Select
    {
        $select = $this->connectionProvider->getSelect();
        $select->from(
            $this->connectionProvider->getTable('inventory_reservation'),
            [
                'sku',
                'stock_id',
                'reservation_qty' => 'SUM(quantity)'
            ]
        );
        $select->where('sku IN (?)', $skus);
        $select->where('stock_id = ?', $stockId);;
        return $select->group('sku');
    }

    /**
     * Get is salable expression
     *
     * @param \Magento\Framework\DB\Select $select
     * @return string
     */
    private function getIsSalableExpression(\Magento\Framework\DB\Select $select): string
    {
        return $this->getIsStockItemSalableCondition->execute($select);
    }
}
