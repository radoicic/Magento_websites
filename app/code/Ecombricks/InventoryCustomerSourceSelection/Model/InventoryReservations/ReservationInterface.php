<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations;

/**
 * Reservation interface
 */
interface ReservationInterface extends \Magento\InventoryReservationsApi\Model\ReservationInterface
{
    /**
     * Source code key
     */
    const SOURCE_CODE = 'source_code';

    /**
     * Get source code
     * 
     * @return string
     */
    public function getSourceCode(): string;
}