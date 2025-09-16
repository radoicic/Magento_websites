<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Plugin\Model\Inventory\ResourceModel\SourceItem\Collection;

/**
 * Source item collection option plugin
 */
class Option
{
    /**
     * Option meta
     * 
     * @var \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta
     */
    private $optionMeta;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta $optionMeta
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta $optionMeta
    )
    {
        $this->optionMeta = $optionMeta;
    }
    
    /**
     * Around add field to select
     * 
     * @param \Magento\Inventory\Model\ResourceModel\SourceItem\Collection $subject
     * @param callable $proceed
     * @param string|array $field
     * @param string|null $alias
     * @return \Magento\Inventory\Model\ResourceModel\SourceItem\Collection
     */
    public function aroundAddFieldToSelect(
        \Magento\Inventory\Model\ResourceModel\SourceItem\Collection $subject,
        callable $proceed,
        $field,
        $alias = null
    )
    {
        $optionName = $this->optionMeta->getName();
        if ($field !== $optionName) {
            if (in_array($field, ['sku', 'source_code', 'quantity', 'status'])) {
                $subject->addFilterToMap($field, $this->getMainTableAlias($subject).'.'.$field);
            }
            return $proceed($field, $alias);
        }
        $this->addOptionJoin($subject);
        $columnName = $optionName;
        $column = $optionName.'.'.\Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::VALUE;
        $subject->addFilterToMap($columnName, $column);
        $subject->getSelect()->columns([$columnName => $column]);
        return $subject;
    }

    /**
     * Get main table alias
     *
     * @param \Magento\Inventory\Model\ResourceModel\SourceItem\Collection $collection
     * @return string
     * @throws \LogicException
     */
    private function getMainTableAlias(\Magento\Inventory\Model\ResourceModel\SourceItem\Collection $collection): string
    {
        foreach ($collection->getSelect()->getPart(\Magento\Framework\DB\Select::FROM) as $tableAlias => $tableMetadata) {
            if ($tableMetadata['joinType'] === 'from') {
                return $tableAlias;
            }
        }
        throw new \LogicException('Main table cannot be identified.');
    }
    
    /**
     * Add option join
     * 
     * @param \Magento\Inventory\Model\ResourceModel\SourceItem\Collection $collection
     * @return void
     */
    private function addOptionJoin(\Magento\Inventory\Model\ResourceModel\SourceItem\Collection $collection): void
    {
        $optionName = $this->optionMeta->getName();
        $select = $collection->getSelect();
        $selectFrom = $select->getPart(\Magento\Framework\DB\Select::FROM);
        if (isset($selectFrom[$optionName])) {
            return;
        }
        $mainTableAlias = $this->getMainTableAlias($collection);
        $skuField = \Magento\InventoryApi\Api\Data\SourceItemInterface::SKU;
        $sourceCodeField = \Magento\InventoryApi\Api\Data\SourceItemInterface::SOURCE_CODE;
        $select->joinLeft(
            [
                $optionName => $collection->getResource()->getTable($this->optionMeta->getTableName()),
            ],
            implode(
                ' AND ',
                [
                    $mainTableAlias.'.'.$skuField.' = '.$optionName.'.'.$skuField,
                    $mainTableAlias.'.'.$sourceCodeField.' = '.$optionName.'.'.$sourceCodeField,
                ]
            ),
            []
        );
    }
}