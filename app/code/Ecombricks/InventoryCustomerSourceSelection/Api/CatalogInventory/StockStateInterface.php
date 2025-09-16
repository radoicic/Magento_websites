<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Api\CatalogInventory;

/**
 * Stock state interface
 */
interface StockStateInterface extends \Magento\CatalogInventory\Api\StockStateInterface
{
    /**
     * Check quote item source qty
     *
     * @param int $productId
     * @param string $sourceCode
     * @param float $itemQty
     * @param float $qtyToCheck
     * @param float $origQty
     * @return \Magento\Framework\DataObject
     */
    public function checkQuoteItemSourceQty($productId, $sourceCode, $itemQty, $qtyToCheck, $origQty);
    
    /**
     * Suggest source qty
     *
     * @param int $productId
     * @param string $sourceCode
     * @param float $qty
     * @return float
     */
    public function suggestSourceQty($productId, $sourceCode, $qty);
}