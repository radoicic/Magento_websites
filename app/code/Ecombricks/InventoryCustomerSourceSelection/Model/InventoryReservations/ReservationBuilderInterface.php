<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations;

/**
 * Reservation builder interface
 */
interface ReservationBuilderInterface extends \Magento\InventoryReservationsApi\Model\ReservationBuilderInterface
{
    /**
     * Set source code
     * 
     * @param string $sourceCode
     * @return \Magento\InventoryReservationsApi\Model\ReservationBuilderInterface
     */
    public function setSourceCode(string $sourceCode): \Magento\InventoryReservationsApi\Model\ReservationBuilderInterface;
}
