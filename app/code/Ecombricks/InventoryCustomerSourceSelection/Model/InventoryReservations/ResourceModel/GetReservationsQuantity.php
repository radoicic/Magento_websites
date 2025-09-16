<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ResourceModel;

/**
 * Get reservations quantity
 */
class GetReservationsQuantity implements \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\GetReservationsQuantityInterface
{
    /**
     * Connection provider
     * 
     * @var \Ecombricks\Common\Model\ResourceModel\ConnectionProvider 
     */
    private $connectionProvider;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider
    )
    {
        $this->connectionProvider = $connectionProvider;
    }

    /**
     * Execute
     *
     * @param string $sku
     * @param string $sourceCode
     * @return float
     */
    public function execute(string $sku, string $sourceCode): float
    {
        $connection = $this->connectionProvider->getConnection();
        $reservationTable = $this->connectionProvider->getTable('ecombricks_inventory__source_inventory_reservation');
        $quantity = \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface::QUANTITY;
        $select = $connection->select();
        $select->from($reservationTable, [$quantity => 'SUM('.$quantity.')']);
        $select->where(\Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface::SKU . ' = ?', $sku);
        $select->where(\Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface::SOURCE_CODE . ' = ?', $sourceCode);
        $select->limit(1);
        $qty = $connection->fetchOne($select);
        return ($qty !== false) ? (float) $qty : 0;
    }
}