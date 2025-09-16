<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventorySales\ResourceModel;

/**
 * Update reservations by SKUs plugin
 */
class UpdateReservationsBySkus
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
     * @param \Magento\InventorySales\Model\ResourceModel\UpdateReservationsBySkus $subject
     * @param callable $proceed
     * @param array $skus
     * @return void
     */
    public function aroundExecute(
        \Magento\InventorySales\Model\ResourceModel\UpdateReservationsBySkus $subject,
        callable $proceed,
        array $skus
    ): void
    {
        foreach ($skus as $sku) {
            $connection = $this->connectionProvider->getConnection();
            $connection->update(
                $this->connectionProvider->getTable('ecombricks_inventory__source_inventory_reservation'),
                [ 'sku' => $sku->getNew() ],
                [ 'sku = ?' => $sku->getOld() ]
            );
        }
    }
}