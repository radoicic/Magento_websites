<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales;

/**
 * Is product salable for requested qty interface
 */
interface IsProductSalableForRequestedQtyInterface
{
    /**
     * Execute
     * 
     * @param string $sku
     * @param string $sourceCode
     * @param float $requestedQty
     * @return \Magento\InventorySalesApi\Api\Data\ProductSalableResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(string $sku, string $sourceCode, float $requestedQty): \Magento\InventorySalesApi\Api\Data\ProductSalableResultInterface;
}