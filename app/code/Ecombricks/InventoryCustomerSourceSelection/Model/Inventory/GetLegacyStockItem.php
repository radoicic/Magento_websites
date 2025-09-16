<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Inventory;

/**
 * Get legacy stock item
 */
class GetLegacyStockItem
{
    /**
     * Get stock ID by store
     * 
     * @var \Ecombricks\InventoryCommon\Api\GetStockIdByStoreInterface 
     */
    private $getStockIdByStore;

    /**
     * Get enabled source codes by stock ID
     * 
     * @var type 
     */
    private $getEnabledSourceCodesByStockId;
    
    /**
     * Stock registry
     * 
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * Module manager
     * 
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Api\GetStockIdByStoreInterface $getStockIdByStore
     * @param \Ecombricks\InventoryCommon\Model\GetEnabledSourceCodesByStockId $getEnabledSourceCodesByStockId
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Api\GetStockIdByStoreInterface $getStockIdByStore,
        \Ecombricks\InventoryCommon\Model\GetEnabledSourceCodesByStockId $getEnabledSourceCodesByStockId,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Module\Manager $moduleManager
    )
    {
        $this->getStockIdByStore = $getStockIdByStore;
        $this->getEnabledSourceCodesByStockId = $getEnabledSourceCodesByStockId;
        $this->stockRegistry = $stockRegistry;
        $this->moduleManager = $moduleManager;
    }
    
    /**
     * Execute
     * 
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param \Magento\Store\Api\Data\StoreInterface|null $store
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface
     */
    public function execute(
        \Magento\Catalog\Api\Data\ProductInterface $product,
        \Magento\Store\Api\Data\StoreInterface $store = null
    ): \Magento\CatalogInventory\Api\Data\StockItemInterface
    {
        if ($store === null) {
            $store = $product->getStore();
        }
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $store->getWebsiteId());
        if (!$this->moduleManager->isEnabled('Ecombricks_InventoryCatalog')) {
            return $stockItem;
        }
        $stockItemExtension = $stockItem->getExtensionAttributes();
        $stockItemExtension->setProductSku($product->getData(\Magento\Catalog\Api\Data\ProductInterface::SKU));
        $sourceCode = (string) $product->getSourceCode();
        if ($sourceCode) {
            $stockItemExtension->setSourceCodes([$sourceCode]);
            return $stockItem;
        }
        if ((int) $store->getId() === \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
            $stockItemExtension->setSourceCodes([]);
            return $stockItem;
        }
        $stockItemExtension->setSourceCodes($this->getEnabledSourceCodesByStockId->execute($this->getStockIdByStore->execute($store)));
        return $stockItem;
    }
}