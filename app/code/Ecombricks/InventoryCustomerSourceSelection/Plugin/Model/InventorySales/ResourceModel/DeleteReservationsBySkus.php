<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventorySales\ResourceModel;

/**
 * Delete reservations by SKUs plugin
 */
class DeleteReservationsBySkus
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
     * @param \Magento\InventorySales\Model\ResourceModel\DeleteReservationsBySkus $subject
     * @param callable $proceed
     * @param array $skus
     * @return void
     */
    public function aroundExecute(
        \Magento\InventorySales\Model\ResourceModel\DeleteReservationsBySkus $subject,
        callable $proceed,
        array $skus
    ): void
    {
        $connection = $this->connectionProvider->getConnection();
        $connection->delete(
            $this->connectionProvider->getTable('ecombricks_inventory__source_inventory_reservation'),
            $connection->quoteInto('sku IN (?)', $skus)
        );
    }
}