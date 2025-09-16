<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Ecombricks\InventoryCustomerSourceSelection\Model\CatalogInventory\Quote\Item\QuantityValidator;

/**
 * Quote item quantity validator list
 */
class QuoteItemQtyList extends \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\QuoteItemQtyList 
{
    
    /**
     * Checked source quote items
     *
     * @var array
     */
    protected $checkedSourceQuoteItems = [];
    
    /**
     * Get qty
     * 
     * @param integer $productId
     * @param string $sourceCode
     * @param integer $quoteItemId
     * @param integer $quoteId
     * @param float $itemQty
     * @return int
     */
    public function getSourceQty($productId, $sourceCode, $quoteItemId, $quoteId, $itemQty)
    {
        $qty = $itemQty;
        if (
            isset($this->checkedSourceQuoteItems[$quoteId][$productId][$sourceCode]['qty']) && 
            !in_array($quoteItemId, $this->checkedSourceQuoteItems[$quoteId][$productId][$sourceCode]['items'])
        ) {
            $qty += $this->checkedSourceQuoteItems[$quoteId][$productId][$sourceCode]['qty'];
        }
        $this->checkedSourceQuoteItems[$quoteId][$productId][$sourceCode]['qty'] = $qty;
        $this->checkedSourceQuoteItems[$quoteId][$productId][$sourceCode]['items'][] = $quoteItemId;
        return $qty;
    }
    
}