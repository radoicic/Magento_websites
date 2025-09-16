<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Inventory;

/**
 * Get stock item configuration
 */
class GetStockItemConfiguration
{
    /**
     * Get stock ID by source code
     * 
     * @var \Ecombricks\InventoryCommon\Model\GetStockIdBySourceCode
     */
    private $getStockIdBySourceCode;
    
    /**
     * Is product assigned to source
     * 
     * @var \Ecombricks\InventoryCommon\Model\ResourceModel\IsProductAssignedToSource
     */
    private $isProductAssignedToSource;
    
    /**
     * Module manager
     * 
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;
    
    /**
     * Default stock provider
     * 
     * @var \Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface
     */
    private $defaultStockProvider;
    
    /**
     * Is source item management allowed for SKU
     * 
     * @var \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForSkuInterface
     */
    private $isSourceItemManagementAllowedForSku;
    
    /**
     * Get legacy stock item
     * 
     * @var \Magento\InventoryConfiguration\Model\GetLegacyStockItem
     */
    private $getLegacyStockItem;

    /**
     * Stock item configuration factory
     * 
     * @var \Magento\InventoryConfiguration\Model\StockItemConfigurationFactory
     */
    private $stockItemConfigurationFactory;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Model\GetStockIdBySourceCode $getStockIdBySourceCode
     * @param \Ecombricks\InventoryCommon\Model\ResourceModel\IsProductAssignedToSource $isProductAssignedToSource
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface $defaultStockProvider
     * @param \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForSkuInterface $isSourceItemManagementAllowedForSku
     * @param \Magento\InventoryConfiguration\Model\GetLegacyStockItem $getLegacyStockItem
     * @param \Magento\InventoryConfiguration\Model\StockItemConfigurationFactory $stockItemConfigurationFactory
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Model\GetStockIdBySourceCode $getStockIdBySourceCode,
        \Ecombricks\InventoryCommon\Model\ResourceModel\IsProductAssignedToSource $isProductAssignedToSource,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface $defaultStockProvider,
        \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForSkuInterface $isSourceItemManagementAllowedForSku,
        \Magento\InventoryConfiguration\Model\GetLegacyStockItem $getLegacyStockItem,
        \Magento\InventoryConfiguration\Model\StockItemConfigurationFactory $stockItemConfigurationFactory
    )
    {
        $this->getStockIdBySourceCode = $getStockIdBySourceCode;
        $this->isProductAssignedToSource = $isProductAssignedToSource;
        $this->moduleManager = $moduleManager;
        $this->defaultStockProvider = $defaultStockProvider;
        $this->isSourceItemManagementAllowedForSku = $isSourceItemManagementAllowedForSku;
        $this->getLegacyStockItem = $getLegacyStockItem;
        $this->stockItemConfigurationFactory = $stockItemConfigurationFactory;
    }

    /**
     * Execute
     * 
     * @param string $sku
     * @param string $sourceCode
     * @return \Magento\InventoryConfigurationApi\Api\Data\StockItemConfigurationInterface
     * @throws \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException
     */
    public function execute(string $sku, string $sourceCode): \Magento\InventoryConfigurationApi\Api\Data\StockItemConfigurationInterface
    {
        $stockId = $this->getStockIdBySourceCode->execute($sourceCode);
        if (
            $this->defaultStockProvider->getId() !== $stockId && 
            true === $this->isSourceItemManagementAllowedForSku->execute($sku) && 
            false === $this->isProductAssignedToSource->execute($sku, $sourceCode)
        ) {
            throw new \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException(
                __('The requested sku is not assigned to given source.')
            );
        }
        $stockItem = $this->getLegacyStockItem->execute($sku);
        if ($this->moduleManager->isEnabled('Ecombricks_InventoryCatalog')) {
            $stockItemExtension = $stockItem->getExtensionAttributes();
            $stockItemExtension->setProductSku($sku);
            $stockItemExtension->setSourceCodes([$sourceCode]);
        }
        return $this->stockItemConfigurationFactory->create(['stockItem' => $stockItem]);
    }
}
