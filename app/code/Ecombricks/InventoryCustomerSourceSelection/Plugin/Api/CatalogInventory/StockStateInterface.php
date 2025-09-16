<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Api\CatalogInventory;

/**
 * Stock state interface plugin
 */
class StockStateInterface
{
    /**
     * Get product salable qty
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales\GetProductSalableQtyInterface
     */
    private $getProductSalableQty;

    /**
     * Is product salable for requested qty
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales\IsProductSalableForRequestedQtyInterface
     */
    private $isProductSalableForRequestedQty;

    /**
     * Back order notify customer condition
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\IsProductSalableForRequestedQtyCondition\BackOrderNotifyCustomerCondition
     */
    private $backOrderNotifyCustomerCondition;

    /**
     * Get stock item configuration
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\GetStockItemConfiguration
     */
    private $getStockItemConfiguration;

    /**
     * Product metadata
     * 
     * @var \Magento\Framework\App\ProductMetadata 
     */
    private $productMetadata;

    /**
     * Object factory
     * 
     * @var \Magento\Framework\DataObject\Factory
     */
    private $objectFactory;

    /**
     * Format
     * 
     * @var \Magento\Framework\Locale\FormatInterface
     */
    private $format;

    /**
     * Get SKUs by product IDs
     * 
     * @var \Magento\InventoryCatalogApi\Model\GetSkusByProductIdsInterface
     */
    private $getSkusByProductIds;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales\GetProductSalableQtyInterface $getProductSalableQty
     * @param \Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales\IsProductSalableForRequestedQtyInterface $isProductSalableForRequestedQty
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\IsProductSalableForRequestedQtyCondition\BackOrderNotifyCustomerCondition $backOrderNotifyCustomerCondition
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\GetStockItemConfiguration $getStockItemConfiguration
     * @param \Magento\Framework\App\ProductMetadata $productMetadata
     * @param \Magento\Framework\DataObject\Factory $objectFactory
     * @param \Magento\Framework\Locale\FormatInterface $format
     * @param \Magento\InventoryCatalogApi\Model\GetSkusByProductIdsInterface $getSkusByProductIds
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales\GetProductSalableQtyInterface $getProductSalableQty,
        \Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales\IsProductSalableForRequestedQtyInterface $isProductSalableForRequestedQty,
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\IsProductSalableForRequestedQtyCondition\BackOrderNotifyCustomerCondition $backOrderNotifyCustomerCondition,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\GetStockItemConfiguration $getStockItemConfiguration,
        \Magento\Framework\App\ProductMetadata $productMetadata,
        \Magento\Framework\DataObject\Factory $objectFactory,
        \Magento\Framework\Locale\FormatInterface $format,
        \Magento\InventoryCatalogApi\Model\GetSkusByProductIdsInterface $getSkusByProductIds
    )
    {
        $this->getProductSalableQty = $getProductSalableQty;
        $this->isProductSalableForRequestedQty = $isProductSalableForRequestedQty;
        $this->backOrderNotifyCustomerCondition = $backOrderNotifyCustomerCondition;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
        $this->productMetadata = $productMetadata;
        $this->objectFactory = $objectFactory;
        $this->format = $format;
        $this->getSkusByProductIds = $getSkusByProductIds;
    }

    /**
     * Around suggest qty
     *
     * @param \Magento\CatalogInventory\Api\StockStateInterface $subject
     * @param \Closure $proceed
     * @param int $productId
     * @param string $sourceCode
     * @param float $qty
     * @return float
     */
    public function aroundSuggestSourceQty(
        \Magento\CatalogInventory\Api\StockStateInterface $subject,
        \Closure $proceed,
        $productId,
        $sourceCode,
        $qty
    ): float
    {
        try {
            $skus = $this->getSkusByProductIds->execute([$productId]);
            $sku = $skus[$productId];
            $stockItemConfiguration = $this->getStockItemConfiguration->execute($sku, (string) $sourceCode);
            $qtyIncrements = $stockItemConfiguration->getQtyIncrements();
            if ($qty <= 0 || $stockItemConfiguration->isManageStock() === false || $qtyIncrements < 2) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Wrong condition.'));
            }
            $minQty = max($stockItemConfiguration->getMinSaleQty(), $qtyIncrements);
            $divisibleMin = ceil($minQty / $qtyIncrements) * $qtyIncrements;
            $salableQty = $this->getProductSalableQty->execute($sku, (string) $sourceCode);
            $maxQty = min($salableQty, $stockItemConfiguration->getMaxSaleQty());
            $divisibleMax = floor($maxQty / $qtyIncrements) * $qtyIncrements;
            if ($qty < $minQty || $qty > $maxQty || $divisibleMin > $divisibleMax) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Wrong condition.'));
            }
            $closestDivisibleLeft = floor($qty / $qtyIncrements) * $qtyIncrements;
            $closestDivisibleRight = $closestDivisibleLeft + $qtyIncrements;
            $acceptableLeft = min(max($divisibleMin, $closestDivisibleLeft), $divisibleMax);
            $acceptableRight = max(min($divisibleMax, $closestDivisibleRight), $divisibleMin);
            return abs($acceptableLeft - $qty) < abs($acceptableRight - $qty) ? $acceptableLeft : $acceptableRight;
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            return $qty;
        }
    }
    
    /**
     * Around check quote item source qty
     *
     * @param \Magento\CatalogInventory\Api\StockStateInterface $subject
     * @param \Closure $proceed
     * @param int $productId
     * @param string $sourceCode
     * @param float $itemQty
     * @param float $qtyToCheck
     * @param float $origQty
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundCheckQuoteItemSourceQty(
        \Magento\CatalogInventory\Api\StockStateInterface $subject,
        \Closure $proceed,
        $productId,
        $sourceCode,
        $itemQty,
        $qtyToCheck,
        $origQty
    )
    {
        $result = $this->objectFactory->create();
        $result->setHasError(false);
        $qty = max($this->getNumber($itemQty), $this->getNumber($qtyToCheck));
        $skus = $this->getSkusByProductIds->execute([$productId]);
        $sku = $skus[$productId];
        $isSalableResult = $this->isProductSalableForRequestedQty->execute($sku, (string) $sourceCode, $qty);
        if ($isSalableResult->isSalable() === false) {
            foreach ($isSalableResult->getErrors() as $error) {
                $errorMessage = $error->getMessage();
                $result
                    ->setHasError(true)
                    ->setMessage($errorMessage)
                    ->setQuoteMessage($errorMessage)
                    ->setQuoteMessageIndex('qty');
            }
        } else {
            $productSalableResult = $this->backOrderNotifyCustomerCondition->execute($sku, (string) $sourceCode, $qty);
            if ($productSalableResult->getErrors()) {
                foreach ($productSalableResult->getErrors() as $error) {
                    $result->setMessage($error->getMessage());
                }
            }
        }
        return $result;
    }

    /**
     * Get number
     *
     * @param string|float|int|null $qty
     * @return float|null
     */
    private function getNumber($qty)
    {
        if (is_numeric($qty)) {
            return $qty;
        }
        return $this->format->getNumber($qty);
    }
}