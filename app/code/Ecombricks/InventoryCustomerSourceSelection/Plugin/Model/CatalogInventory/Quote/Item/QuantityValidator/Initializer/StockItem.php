<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\CatalogInventory\Quote\Item\QuantityValidator\Initializer;

/**
 * Quote item quantity validator stock item initializer plugin
 */
class StockItem extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Stock state
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Api\CatalogInventory\StockStateInterface
     */
    private $stockState;

    /**
     * Quote item qty list
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\CatalogInventory\Quote\Item\QuantityValidator\QuoteItemQtyList
     */
    private $quoteItemQtyList;

    /**
     * Quote item wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory
     */
    private $quoteItemWrapperFactory;
    
    /**
     * Stock state provider
     * 
     * @var \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface
     */
    private $stockStateProvider;
    
    /**
     * Type configuration
     * 
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    private $typeConfig;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Api\CatalogInventory\StockStateInterface $stockState
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\CatalogInventory\Quote\Item\QuantityValidator\QuoteItemQtyList $quoteItemQtyList
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory $quoteItemWrapperFactory
     * @param \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface $stockStateProvider
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $typeConfig
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Api\CatalogInventory\StockStateInterface $stockState,
        \Ecombricks\InventoryCustomerSourceSelection\Model\CatalogInventory\Quote\Item\QuantityValidator\QuoteItemQtyList $quoteItemQtyList,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory $quoteItemWrapperFactory,
        \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface $stockStateProvider,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $typeConfig
    )
    {
        parent::__construct($wrapperFactory);
        $this->stockState = $stockState;
        $this->quoteItemQtyList = $quoteItemQtyList;
        $this->quoteItemWrapperFactory = $quoteItemWrapperFactory;
        $this->stockStateProvider = $stockStateProvider;
        $this->typeConfig = $typeConfig;
    }

    /**
     * Around initialize
     * 
     * @param \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\StockItem $subject
     * @param \Closure $proceed
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param int $qty
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundInitialize(
        \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\StockItem $subject,
        \Closure $proceed,
        \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem,
        \Magento\Quote\Model\Quote\Item $quoteItem,
        $qty
    )
    {
        $this->setSubject($subject);
        $this->prepareStockItem($stockItem, $quoteItem);
        $checkQtyResult = $this->checkQuoteItemQty($stockItem, $quoteItem, $qty);
        $this->releaseStockItem($stockItem);
        $this->updateQuoteItem($quoteItem, $checkQtyResult);
        return $checkQtyResult;
    }

    /**
     * Prepare stock item
     * 
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return void
     */
    private function prepareStockItem(\Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem, \Magento\Quote\Model\Quote\Item $quoteItem): void
    {
        $product = $quoteItem->getProduct();
        $stockItem->setProductName($product->getName());
        $productTypeOption = $product->getCustomOption('product_type');
        if (empty($productTypeOption)) {
            return;
        }
        if ($this->typeConfig->isProductSet($productTypeOption->getValue())) {
            $stockItem->setIsChildItem(true);
        }
    }
    
    /**
     * Release stock item
     * 
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @return void
     */
    private function releaseStockItem(\Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem): void
    {
        if ($stockItem->hasIsChildItem()) {
            $stockItem->unsIsChildItem();
        }
    }
    
    /**
     * Check quote item qty
     * 
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param float $qty
     * @return \Magento\Framework\DataObject
     */
    private function checkQuoteItemQty(
        \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem,
        \Magento\Quote\Model\Quote\Item $quoteItem,
        $qty
    )
    {
        $sourceCode = $this->quoteItemWrapperFactory->create($quoteItem)->getSourceCode();
        $product = $quoteItem->getProduct();
        $productId = $product->getId();
        $store = $product->getStore();
        $parentQuoteItem = $quoteItem->getParentItem();
        $qtyToAdd = $quoteItem->getQtyToAdd();
        $qtyForCheck = $this->quoteItemQtyList->getSourceQty(
            $productId,
            $sourceCode,
            $quoteItem->getId(),
            $quoteItem->getQuoteId(),
            $parentQuoteItem ? 0 : ($qtyToAdd ? $qtyToAdd : $qty)
        );
        $checkQtyResult = $this->stockState->checkQuoteItemSourceQty(
            $productId,
            $sourceCode,
            ($parentQuoteItem) ? $parentQuoteItem->getQty() * $qty : $qty,
            $qtyForCheck,
            $qty,
            $store->getWebsiteId()
        );
        if ($checkQtyResult->getHasError() === true && in_array($checkQtyResult->getErrorCode(), ['qty_available', 'out_stock'])) {
            $quoteItem->setHasError(true);
        }
        $checkQtyResult->setItemBackorders(
            $this->stockStateProvider->checkQuoteItemQty(
                    $stockItem,
                    ($parentQuoteItem) ? $parentQuoteItem->getQty() * $qty : $qty,
                    $qtyForCheck,
                    $qty
                )
                ->getItemBackorders()
        );
        return $checkQtyResult;
    }
    
    /**
     * Update quote item
     * 
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param \Magento\Framework\DataObject $checkQtyResult
     * @return void
     */
    private function updateQuoteItem(\Magento\Quote\Model\Quote\Item $quoteItem, \Magento\Framework\DataObject $checkQtyResult): void
    {
        $parentQuoteItem = $quoteItem->getParentItem();
        $parentProduct = ($parentQuoteItem) ? $parentQuoteItem->getProduct() : null;
        $isQtyDecimal = $checkQtyResult->getItemIsQtyDecimal();
        if ($isQtyDecimal !== null) {
            $quoteItem->setIsQtyDecimal($isQtyDecimal);
            if ($parentQuoteItem) {
                $parentQuoteItem->setIsQtyDecimal($isQtyDecimal);
            }
        }
        if (
            $checkQtyResult->getHasQtyOptionUpdate() && 
            (!$parentQuoteItem || $parentProduct->getTypeInstance()->getForceChildItemQtyChanges($parentProduct))
        ) {
            $quoteItem->setData('qty', $checkQtyResult->getOrigQty());
        }
        $useOldQty = $checkQtyResult->getItemUseOldQty();
        if ($useOldQty !== null) {
            $quoteItem->setUseOldQty($useOldQty);
        }
        $message = $checkQtyResult->getMessage();
        if ($message !== null) {
            $quoteItem->setMessage($message);
        }
        $backorders = $checkQtyResult->getItemBackorders();
        if ($backorders !== null) {
            $quoteItem->setBackorders($backorders);
        }
        $quoteItem->setStockStateResult($checkQtyResult);
    }
}