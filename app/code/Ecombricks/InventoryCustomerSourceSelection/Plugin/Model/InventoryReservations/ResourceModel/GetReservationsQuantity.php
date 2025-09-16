<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventoryReservations\ResourceModel;

/**
 * Get reservations quantity resource plugin
 */
class GetReservationsQuantity
{
    /**
     * Connection provider
     * 
     * @var \Ecombricks\Common\Model\ResourceModel\ConnectionProvider
     */
    private $connectionProvider;

    /**
     * Get enabled sources by stock ID
     * 
     * @var \Ecombricks\InventoryCommon\Model\GetEnabledSourcesByStockId
     */
    private $getEnabledSourcesByStockId;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider
     * @param \Ecombricks\InventoryCommon\Model\GetEnabledSourcesByStockId $getEnabledSourcesByStockId
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider,
        \Ecombricks\InventoryCommon\Model\GetEnabledSourcesByStockId $getEnabledSourcesByStockId
    )
    {
        $this->connectionProvider = $connectionProvider;
        $this->getEnabledSourcesByStockId = $getEnabledSourcesByStockId;
    }

    /**
     * Around execute
     * 
     * @param \Magento\InventoryReservations\Model\ResourceModel\GetReservationsQuantity $subject
     * @param \Closure $proceed
     * @param string $sku
     * @param int $stockId
     * @return float
     */
    public function aroundExecute(
        \Magento\InventoryReservations\Model\ResourceModel\GetReservationsQuantity $subject,
        \Closure $proceed,
        string $sku,
        int $stockId
    ): float
    {
        $connection = $this->connectionProvider->getConnection();
        $table = $this->connectionProvider->getTable('ecombricks_inventory__source_inventory_reservation');
        $quantity = \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface::QUANTITY;
        $sources = $this->getEnabledSourcesByStockId->execute($stockId);
        $select = $connection->select()
            ->from($table, [$quantity => 'SUM('.$quantity.')'])
            ->where(\Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface::SKU . ' = ?', $sku)
            ->where(\Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface::SOURCE_CODE . ' IN (?)', array_keys($sources))
            ->limit(1);
        $qty = $connection->fetchOne($select);
        return ($qty !== false) ? (float) $qty : 0;
    }
}