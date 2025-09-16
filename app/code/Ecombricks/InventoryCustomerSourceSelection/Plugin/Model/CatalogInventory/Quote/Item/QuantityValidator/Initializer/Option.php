<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\CatalogInventory\Quote\Item\QuantityValidator\Initializer;

/**
 * Quote item quantity validator option initializer plugin
 */
class Option extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Quote item qty list
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\CatalogInventory\Quote\Item\QuantityValidator\QuoteItemQtyList
     */
    private $quoteItemQtyList;
    
    /**
     * Stock state
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Api\CatalogInventory\StockStateInterface
     */
    private $stockState;
    
    /**
     * Quote item wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory
     */
    private $quoteItemWrapperFactory;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\CatalogInventory\Quote\Item\QuantityValidator\QuoteItemQtyList $quoteItemQtyList
     * @param \Ecombricks\InventoryCustomerSourceSelection\Api\CatalogInventory\StockStateInterface $stockState
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory $quoteItemWrapperFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Model\CatalogInventory\Quote\Item\QuantityValidator\QuoteItemQtyList $quoteItemQtyList,
        \Ecombricks\InventoryCustomerSourceSelection\Api\CatalogInventory\StockStateInterface $stockState,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory $quoteItemWrapperFactory
    )
    {
        parent::__construct($wrapperFactory);
        $this->quoteItemQtyList = $quoteItemQtyList;
        $this->stockState = $stockState;
        $this->quoteItemWrapperFactory = $quoteItemWrapperFactory;
    }

    /**
     * Around initialize
     * 
     * @param \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\Option $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Item\Option $option
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param int $qty
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundInitialize(
        \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\Option $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\Option $option,
        \Magento\Quote\Model\Quote\Item $quoteItem,
        $qty
    )
    {
        $this->setSubject($subject);
        $stockItem = $subject->getStockItem($option, $quoteItem);
        $this->prepareStockItem($stockItem, $option);
        $checkQtyResult = $this->checkOptionQty($option, $quoteItem, $qty);
        $this->releaseStockItem($stockItem);
        $this->updateOption($option, $quoteItem, $checkQtyResult, $qty);
        return $checkQtyResult;
    }

    /**
     * Prepare stock item
     * 
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @param \Magento\Quote\Model\Quote\Item\Option $option
     * @return void
     */
    private function prepareStockItem(\Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem, \Magento\Quote\Model\Quote\Item\Option $option): void
    {
        $stockItem->setProductName($option->getProduct()->getName());
    }
    
    /**
     * Release stock item
     * 
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @return void
     */
    private function releaseStockItem(\Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem): void
    {
        $stockItem->unsIsChildItem();
    }
    
    /**
     * Check option qty
     * 
     * @param \Magento\Quote\Model\Quote\Item\Option $option
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param float $qty
     * @return \Magento\Framework\DataObject
     */
    private function checkOptionQty(
        \Magento\Quote\Model\Quote\Item\Option $option,
        \Magento\Quote\Model\Quote\Item $quoteItem,
        $qty
    )
    {
        $sourceCode = (string) $this->quoteItemWrapperFactory->create($quoteItem)->getSourceCode();
        $product = $option->getProduct();
        $productId = $product->getId();
        $store = $product->getStore();
        $optionValue = $option->getValue();
        $qtyToAdd = $quoteItem->getQtyToAdd();
        $qtyForCheck = $this->quoteItemQtyList->getSourceQty(
            $productId,
            $sourceCode,
            $quoteItem->getId(),
            $quoteItem->getQuoteId(),
            ($qtyToAdd ? $qtyToAdd : $qty) * $optionValue
        );
        return $this->stockState->checkQuoteItemSourceQty(
            $productId,
            $sourceCode,
            $qty * $optionValue,
            $qtyForCheck,
            $optionValue,
            $store->getWebsiteId()
        );
    }
    
    /**
     * Update option
     * 
     * @param \Magento\Quote\Model\Quote\Item\Option $option
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param \Magento\Framework\DataObject $checkQtyResult
     * @param int $qty
     * @return void
     */
    private function updateOption(
        \Magento\Quote\Model\Quote\Item\Option $option,
        \Magento\Quote\Model\Quote\Item $quoteItem,
        \Magento\Framework\DataObject $checkQtyResult,
        $qty
    ): void
    {
        $isQtyDecimal = $checkQtyResult->getItemIsQtyDecimal();
        if ($isQtyDecimal !== null) {
            $option->setIsQtyDecimal($isQtyDecimal);
        }
        if ($checkQtyResult->getHasQtyOptionUpdate()) {
            $origQty = $checkQtyResult->getOrigQty();
            $option->setHasQtyOptionUpdate(true);
            $quoteItem->updateQtyOption($option, $origQty);
            $option->setValue($origQty);
            $quoteItem->setData('qty', (int) $qty);
        }
        $message = $checkQtyResult->getMessage();
        if ($message !== null) {
            $option->setMessage($message);
            $quoteItem->setMessage($message);
        }
        $backorders = $checkQtyResult->getItemBackorders();
        if ($backorders !== null) {
            $option->setBackorders($backorders);
        }
        $option->setStockStateResult($checkQtyResult);
    }
}