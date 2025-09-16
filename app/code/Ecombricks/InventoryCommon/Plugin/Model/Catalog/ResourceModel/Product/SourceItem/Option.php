<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Plugin\Model\Catalog\ResourceModel\Product\SourceItem;

/**
 * Product resource source item option plugin
 */
class Option
{
    /**
     * Get source item options
     * 
     * @var \Ecombricks\InventoryCommon\Api\SourceItem\Option\GetInterface
     */
    private $getOptions;

    /**
     * Save source item options
     * 
     * @var \Ecombricks\InventoryCommon\Api\SourceItem\Option\SaveInterface
     */
    private $saveOptions;

    /**
     * Source item option configuration
     * 
     * @var \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config
     */
    private $optionConfig;

    /**
     * Source item option meta
     * 
     * @var \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta
     */
    private $optionMeta;

    /**
     * Options processor
     * 
     * @var \Ecombricks\InventoryCommon\Model\SourceItem\Option\Processor
     */
    private $optionsProcessor;

    /**
     * Default source provider
     * 
     * @var \Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface
     */
    private $defaultSourceProvider;

    /**
     * Is source item management allowed for product type
     * 
     * @var \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface
     */
    private $isSourceItemManagementAllowedForProductType;

    /**
     * Logger
     * 
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Api\SourceItem\Option\GetInterface $getOptions
     * @param \Ecombricks\InventoryCommon\Api\SourceItem\Option\SaveInterface $saveOptions
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config $optionConfig
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta $optionMeta
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Processor $optionsProcessor
     * @param \Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface $defaultSourceProvider
     * @param \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param \Psr\Log\LoggerInterface $logger
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Api\SourceItem\Option\GetInterface $getOptions,
        \Ecombricks\InventoryCommon\Api\SourceItem\Option\SaveInterface $saveOptions,
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config $optionConfig,
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta $optionMeta,
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Processor $optionsProcessor,
        \Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface $defaultSourceProvider,
        \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->getOptions = $getOptions;
        $this->saveOptions = $saveOptions;
        $this->optionConfig = $optionConfig;
        $this->optionMeta = $optionMeta;
        $this->optionsProcessor = $optionsProcessor;
        $this->defaultSourceProvider = $defaultSourceProvider;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->logger = $logger;
    }

    /**
     * After save
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product $subject
     * @param \Magento\Catalog\Model\ResourceModel\Product $result
     * @param \Magento\Framework\Model\AbstractModel $product
     * @return \Magento\Catalog\Model\ResourceModel\Product
     */
    public function afterSave(
        \Magento\Catalog\Model\ResourceModel\Product $subject,
        \Magento\Catalog\Model\ResourceModel\Product $result,
        \Magento\Framework\Model\AbstractModel $product
    ): \Magento\Catalog\Model\ResourceModel\Product
    {
        return $this->saveDefault($this->updateSku($result, $product), $product);
    }

    /**
     * Update SKU
     * 
     * @param \Magento\Catalog\Model\ResourceModel\Product $result
     * @param \Magento\Framework\Model\AbstractModel $product
     * @return \Magento\Catalog\Model\ResourceModel\Product
     */
    private function updateSku(
        \Magento\Catalog\Model\ResourceModel\Product $result,
        \Magento\Framework\Model\AbstractModel $product
    ): \Magento\Catalog\Model\ResourceModel\Product
    {
        if (!$this->optionConfig->isEnabled()) {
            return $result;
        }
        $origSku = (string) $product->getOrigData('sku');
        if (!$origSku || $origSku === $product->getSku()) {
            return $result;
        }
        $options = $this->getOptions->execute([$origSku])[$origSku] ?? [];
        foreach ($options as $option) {
            $option->setSku($product->getSku());
        }
        if ($options) {
            $this->saveOptions->execute($options);
        }
        return $result;
    }
    
    /**
     * Save default
     * 
     * @param \Magento\Catalog\Model\ResourceModel\Product $result
     * @param \Magento\Framework\Model\AbstractModel $product
     * @return \Magento\Catalog\Model\ResourceModel\Product
     */
    private function saveDefault(
        \Magento\Catalog\Model\ResourceModel\Product $result,
        \Magento\Framework\Model\AbstractModel $product
    ): \Magento\Catalog\Model\ResourceModel\Product
    {
        if (!$this->optionConfig->isEnabled()) {
            return $result;
        }
        $productTypeId = $product->getTypeId() ?? \Magento\Catalog\Model\Product\Type::DEFAULT_TYPE;
        $stockItem = $product->getExtensionAttributes()->getStockItem();
        if ($this->isSourceItemManagementAllowedForProductType->execute($productTypeId) === false || !$stockItem) {
            return $result;
        }
        $optionName = $this->optionMeta->getName();
        $optionValue = $stockItem->getData($optionName);
        $optionsData[] = [
            \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::SKU => $this->defaultSourceProvider->getCode(),
            $optionName => $optionValue,
            $optionName.'_use_default' => $optionValue === null ? 1 : $stockItem->getData('use_config_'.$optionName),
        ];
        try {
            $this->optionsProcessor->execute($product->getSku(), $optionsData);
        } catch (\Magento\Framework\Exception\InputException $exception) {
            $this->logger->error($exception->getLogMessage());
        }
        return $result;
    }
}