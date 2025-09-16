<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales;

/**
 * Get product salable qty interface
 */
interface GetProductSalableQtyInterface
{
    /**
     * Execute
     * 
     * @param string $sku
     * @param string $sourceCode
     * @return float
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(string $sku, string $sourceCode): float;
}