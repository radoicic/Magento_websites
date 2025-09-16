<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservationCli;

/**
 * Salable quantity inconsistency
 */
class SalableQuantityInconsistency extends \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency
{
    /**
     * Source code
     * 
     * @var string
     */
    private $sourceCode;

    /**
     * Get source code
     * 
     * @return string
     */
    public function getSourceCode(): string
    {
        return $this->sourceCode;
    }

    /**
     * Set source code
     *
     * @param string $sourceCode
     */
    public function setSourceCode(string $sourceCode): void
    {
        $this->sourceCode = $sourceCode;
    }
}