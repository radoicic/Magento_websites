<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventoryReservationCli\ResourceModel;

/**
 * Get order items data for orders in not final state plugin
 */
class GetOrderItemsDataForOrdersInNotFinalState
{
    /**
     * Connection provider
     * 
     * @var \Ecombricks\Common\Model\ResourceModel\ConnectionProvider
     */
    private $connectionProvider;

    /**
     * Get complete order state list
     * 
     * @var \Magento\InventoryReservationCli\Model\GetCompleteOrderStateList 
     */
    private $getCompleteOrderStateList;

    /**
     * Allowed product types for source item management
     * 
     * @var \Magento\InventoryConfigurationApi\Model\GetAllowedProductTypesForSourceItemManagementInterface 
     */
    private $allowedProductTypesForSourceItemManagement;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider
     * @param \Magento\InventoryReservationCli\Model\GetCompleteOrderStateList $getCompleteOrderStateList
     * @param \Magento\InventoryConfigurationApi\Model\GetAllowedProductTypesForSourceItemManagementInterface $allowedProductTypesForSourceItemManagement
     */
    public function __construct(
        \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider,
        \Magento\InventoryReservationCli\Model\GetCompleteOrderStateList $getCompleteOrderStateList,
        \Magento\InventoryConfigurationApi\Model\GetAllowedProductTypesForSourceItemManagementInterface $allowedProductTypesForSourceItemManagement
    )
    {
        $this->connectionProvider = $connectionProvider;
        $this->getCompleteOrderStateList = $getCompleteOrderStateList;
        $this->allowedProductTypesForSourceItemManagement = $allowedProductTypesForSourceItemManagement;
    }

    /**
     * Around execute
     * 
     * @param \Magento\InventoryReservationCli\Model\ResourceModel\GetOrderItemsDataForOrdersInNotFinalState $subject
     * @param callable $proceed
     * @param int $bunchSize
     * @param int $page
     * @return array
     */
    public function aroundExecute(
        \Magento\InventoryReservationCli\Model\ResourceModel\GetOrderItemsDataForOrdersInNotFinalState $subject,
        callable $proceed,
        int $bunchSize = 50,
        int $page = 1
    ): array
    {
        $orderItems = $this->getOrderItems($bunchSize, $page);
        $storeWebsiteIds = $this->getStoreWebsiteIds();
        foreach ($orderItems as $key => $orderItem) {
            $orderItem['website_id'] = $storeWebsiteIds[$orderItem['store_id']];
            $orderItems[$key] = $orderItem;
        }
        return $orderItems;
    }
    
    /**
     * Get order IDs
     * 
     * @param int $bunchSize
     * @param int $page
     * @return array
     */
    private function getOrderIds(int $bunchSize = 50, int $page = 1): array
    {
        $connection = $this->connectionProvider->getConnection('sales');
        return (array) $connection->fetchCol(
            $connection->select()
                ->from(
                    ['order' => $this->connectionProvider->getTable('sales_order', 'sales')],
                    ['order.entity_id']
                )
                ->where('order.state NOT IN (?)', $this->getCompleteOrderStateList->execute())
                ->limitPage($page, $bunchSize)
        );
    }
    
    /**
     * Get order items
     * 
     * @param int $bunchSize
     * @param int $page
     * @return array
     */
    private function getOrderItems(int $bunchSize = 50, int $page = 1): array
    {
        $connection = $this->connectionProvider->getConnection('sales');
        $orderIds = $this->getOrderIds($bunchSize, $page);
        return (array) $connection->fetchAll(
            $connection->select()
            ->from(
                ['order' => $this->connectionProvider->getTable('sales_order', 'sales')],
                [
                    'order.entity_id',
                    'order.increment_id',
                    'order.status',
                    'order.store_id',
                ]
            )
            ->join(
                ['order_item' => $this->connectionProvider->getTable('sales_order_item', 'sales')],
                'order_item.order_id = order.entity_id',
                [
                    'order_item.sku',
                    'order_item.qty_ordered',
                ]
            )
            ->join(
                ['order_item_source' => $this->connectionProvider->getTable('ecombricks_inventory__sales_order_item_source', 'sales')],
                'order_item_source.item_id = order_item.item_id',
                [
                    'order_item_source.source_code',
                ]
            )
            ->where('order.entity_id IN (?)', $orderIds)
            ->where('order_item.product_type IN (?)', $this->allowedProductTypesForSourceItemManagement->execute())
        );
    }

    /**
     * Get store website IDs
     *
     * @return array
     */
    private function getStoreWebsiteIds(): array
    {
        $storeWebsiteIds = [];
        $connection = $this->connectionProvider->getConnection();
        $select = $connection->select()
            ->from(
                ['main_table' => $this->connectionProvider->getTable('store')],
                ['store_id', 'website_id']
            );
        foreach ($connection->fetchAll($select) as $store) {
            $storeWebsiteIds[$store['store_id']] = $store['website_id'];
        }
        return $storeWebsiteIds;
    }
}