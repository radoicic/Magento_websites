<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventoryReservationCli\ResourceModel;

/**
 * Get reservations list plugin
 */
class GetReservationsList
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
     * @param \Magento\InventoryReservationCli\Model\ResourceModel\GetReservationsList $subject
     * @param callable $proceed
     * @return array
     */
    public function aroundExecute(
        \Magento\InventoryReservationCli\Model\ResourceModel\GetReservationsList $subject,
        callable $proceed
    ): array
    {
        $connection = $this->connectionProvider->getConnection();
        return $connection->fetchAll(
            $connection->select()
                ->from($this->connectionProvider->getTable('ecombricks_inventory__source_inventory_reservation'))
        );
    }
}