<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Sales\ResourceModel\Order\Grid;

/**
 * Order grid collection resource plugin
 */
class Collection extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Around add field to filter
     * 
     * @param \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $subject
     * @param \Closure $proceed
     * @param string|array $field
     * @param null|string|array $condition
     * @return \Magento\Sales\Model\ResourceModel\Order\Grid\Collection
     */
    public function aroundAddFieldToFilter(
        \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $subject,
        \Closure $proceed,
        $field,
        $condition = null
    )
    {
        $this->setSubject($subject);
        if (is_array($field) && in_array('source_codes', $field)) {
            $this->addSourceCodeFilter($condition['source_codes']);
            unset($field['source_codes']);
            unset($condition['source_codes']);
        } else if (is_string($field) && $field === 'source_codes') {
            $this->addSourceCodeFilter($condition);
            return $subject;
        }
        return $proceed($field, $condition);
    }

    /**
     * Add source code filter
     * 
     * @param null|string|array $condition
     * @return void
     */
    private function addSourceCodeFilter($condition): void
    {
        $subject = $this->getSubject();
        $connection = $subject->getConnection();
        $select = $subject->getSelect();
        $select->join(
            ['order_item' => $subject->getTable('sales_order_item')],
            'order_item.order_id = main_table.entity_id',
            []
        );
        $select->join(
            ['order_item_source' => $subject->getTable('ecombricks_inventory__sales_order_item_source')],
            'order_item_source.item_id = order_item.item_id',
            []
        );
        $select->where($connection->prepareSqlCondition('order_item_source.source_code', $condition));
    }
}