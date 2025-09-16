<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations;

/**
 * Get reservations quantity interface
 */
interface GetReservationsQuantityInterface
{
    /**
     * Execute
     *
     * @param string $sku
     * @param string $sourceCode
     * @return float
     */
    public function execute(string $sku, string $sourceCode): float;
}