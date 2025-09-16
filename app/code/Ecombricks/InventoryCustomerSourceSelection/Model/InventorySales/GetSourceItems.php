<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales;

/**
 * Get source items
 */
class GetSourceItems
{
    /**
     * Product repository
     * 
     * @var \Magento\Catalog\Model\ProductRepository
     */
    private $productRepository;
    
    /**
     * Get source items resource
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\ResourceModel\SourceItem\Get
     */
    private $getSourceItemsResource;
    
    /**
     * Source item factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\SourceItemInterfaceFactory
     */
    private $sourceItemFactory;

    /**
     * Source items
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\SourceItemInterface[][]
     */
    private $sourceItems = [];
    
    /**
     * Constructor
     * 
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\ResourceModel\SourceItem\Get $getSourceItemsResource
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\SourceItemInterfaceFactory $sourceItemFactory
     * @return void
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\ResourceModel\SourceItem\Get $getSourceItemsResource,
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\SourceItemInterfaceFactory $sourceItemFactory
    )
    {
        $this->productRepository = $productRepository;
        $this->getSourceItemsResource = $getSourceItemsResource;
        $this->sourceItemFactory = $sourceItemFactory;
    }

    /**
     * Execute
     * 
     * @param string $sku
     * @param int $stockId
     * @return \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\SourceItemInterface[]
     */
    public function execute(string $sku, int $stockId): array
    {
        if (
            array_key_exists($sku, $this->sourceItems) && 
            array_key_exists($stockId, $this->sourceItems[$sku])
        ) {
            return $this->sourceItems[$sku][$stockId];
        }
        $this->sourceItems[$sku][$stockId] = [];
        $product = $this->getProduct($sku);
        $sourceItems = $this->loadList($this->getProductSkus($product), $stockId);
        foreach ($this->getSourceItemsSourceCodes($sourceItems) as $sourceCode) {
            $sourceItem = $product->isComposite() ? 
                $this->getCompositeProductSourceItem($product, (string) $sourceCode, $sourceItems):
                $sourceItems[$sku][$sourceCode] ?? null;
            if (empty($sourceItem)) {
                continue;
            }
            $this->sourceItems[$sku][$stockId][$sourceCode] = $sourceItem;
        }
        return $this->sourceItems[$sku][$stockId];
    }

    /**
     * Get product
     * 
     * @param string $sku
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    private function getProduct(string $sku): \Magento\Catalog\Api\Data\ProductInterface
    {
        return $this->productRepository->get($sku);
    }
    
    /**
     * Get product SKU
     * 
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return string
     */
    private function getProductSku(\Magento\Catalog\Api\Data\ProductInterface $product): string
    {
        return $product->getData(\Magento\Catalog\Api\Data\ProductInterface::SKU);
    }
    
    /**
     * Get product SKUs
     * 
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return array
     */
    private function getProductSkus(\Magento\Catalog\Api\Data\ProductInterface $product): array
    {
        $skus = [];
        if ($product->isComposite()) {
            foreach ($product->getTypeInstance()->getProductsToPurchaseByReqGroups($product) as $childGroupProducts) {
                foreach ($childGroupProducts as $childProduct) {
                    $childSku = $this->getProductSku($childProduct);
                    if (in_array($childSku, $skus)) {
                        continue;
                    }
                    $skus[] = $childSku;
                }
            }
        } else {
            $skus[] = $this->getProductSku($product);
        }
        return $skus;
    }

    /**
     * Load list
     * 
     * @param array $skus
     * @param int $stockId
     * @return array
     */
    private function loadList(array $skus, int $stockId): array
    {
        $sourceItems = [];
        $sourceItemsData = $this->getSourceItemsResource->execute($skus, $stockId);
        if (empty($sourceItemsData)) {
            return $sourceItems;
        }
        foreach ($sourceItemsData as $sourceItemData) {
            $sourceItem = $this->sourceItemFactory->create();
            $sourceItem
                ->setSku((string) $sourceItemData['sku'])
                ->setSourceCode((string) $sourceItemData['source_code'])
                ->setQuantity((float) $sourceItemData['quantity'])
                ->setIsSalable((bool) $sourceItemData['is_salable']);
            $sourceItems[$sourceItem->getSku()][$sourceItem->getSourceCode()] = $sourceItem;
        }
        return $sourceItems;
    }
    
    /**
     * Get source items source codes
     * 
     * @param array $sourceItems
     * @return array
     */
    private function getSourceItemsSourceCodes(array $sourceItems): array
    {
        $sourceCodes = [];
        foreach ($sourceItems as $productSourceItems) {
            foreach (array_keys($productSourceItems) as $sourceCode) {
                if (!in_array($sourceCode, $sourceCodes)) {
                    $sourceCodes[] = (string) $sourceCode;
                }
            }
        }
        return $sourceCodes;
    }

    /**
     * Get composite product source item
     * 
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param string $sourceCode
     * @param array $sourceItems
     * @return \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\SourceItemInterface
     */
    private function getCompositeProductSourceItem(
        \Magento\Catalog\Api\Data\ProductInterface $product,
        string $sourceCode,
        array $sourceItems
    ): \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\SourceItemInterface
    {
        $qty = $isSalable = null;
        foreach ($product->getTypeInstance()->getProductsToPurchaseByReqGroups($product) as $childGroupProducts) {
            $childGroupQty = 0;
            $childGroupIsSalable = false;
            foreach ($childGroupProducts as $childProduct) {
                $childSku = $this->getProductSku($childProduct);
                $childSourceItem = $sourceItems[$childSku][$sourceCode] ?? null;
                $childQty = $childSourceItem ? $childSourceItem->getQuantity() : 0;
                $childIsSalable = $childSourceItem ? $childSourceItem->getIsSalable() : false;
                $childGroupQty += $childQty;
                $childGroupIsSalable = $childGroupIsSalable || $childIsSalable;
            }
            $qty = ($qty !== null) ? min($qty, $childGroupQty) : $childGroupQty;
            $isSalable = ($isSalable !== null) ? $isSalable && $childGroupIsSalable : $childGroupIsSalable;
        }
        return $this->sourceItemFactory->create()
            ->setSku($this->getProductSku($product))
            ->setSourceCode($sourceCode)
            ->setQuantity((float) $qty)
            ->setIsSalable((bool) $isSalable);
    }
}