<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventoryReservations\ResourceModel;

/**
 * Save multiple reservations resource plugin
 */
class SaveMultiple
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
     * Around execute
     * 
     * @param \Magento\InventoryReservations\Model\ResourceModel\SaveMultiple $subject
     * @param \Closure $proceed
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface[] $reservations
     * @return void
     */
    public function aroundExecute(
        \Magento\InventoryReservations\Model\ResourceModel\SaveMultiple $subject,
        \Closure $proceed,
        array $reservations
    )
    {
        $data = [];
        foreach ($reservations as $reservation) {
            $data[] = [
                $reservation->getSourceCode(),
                $reservation->getSku(),
                $reservation->getQuantity(),
                $reservation->getMetadata(),
            ];
        }
        $this->connectionProvider->getConnection()->insertArray(
            $this->connectionProvider->getTable('ecombricks_inventory__source_inventory_reservation'),
            [
                \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface::SOURCE_CODE,
                \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface::SKU,
                \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface::QUANTITY,
                \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface::METADATA,
            ], 
            $data
        );
    }
}