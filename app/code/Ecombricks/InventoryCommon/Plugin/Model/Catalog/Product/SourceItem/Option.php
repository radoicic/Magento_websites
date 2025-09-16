<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Plugin\Model\Catalog\Product\SourceItem;

/**
 * Product source item option plugin
 */
class Option
{
    /**
     * Option configuration
     * 
     * @var \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config
     */
    private $optionConfig;

    /**
     * Get options
     * 
     * @var \Ecombricks\InventoryCommon\Model\SourceItem\Option\Get
     */
    private $getOptions;

    /**
     * Option meta
     * 
     * @var \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta
     */
    private $optionMeta;

    /**
     * Get current sources
     * 
     * @var \Ecombricks\InventoryCommon\Model\GetCurrentSources 
     */
    private $getCurrentSources;

    /**
     * Is source item management allowed for product type
     * 
     * @var \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface
     */
    private $isSourceItemManagementAllowedForProductType;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config $optionConfig
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Get $getOptions
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta $optionMeta
     * @param \Ecombricks\InventoryCommon\Model\GetCurrentSources $getCurrentSources
     * @param \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config $optionConfig,
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Get $getOptions,
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta $optionMeta,
        \Ecombricks\InventoryCommon\Model\GetCurrentSources $getCurrentSources,
        \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->optionConfig = $optionConfig;
        $this->getOptions = $getOptions;
        $this->optionMeta = $optionMeta;
        $this->getCurrentSources = $getCurrentSources;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->storeManager = $storeManager;
    }

    /**
     * Around get data
     * 
     * @param \Magento\Catalog\Model\Product $subject
     * @param callable $proceed
     * @param string $key
     * @param string|int $index
     * @return mixed
     */
    public function aroundGetData(
        \Magento\Catalog\Model\Product $subject,
        \Closure $proceed,
        $key = '',
        $index = null
    )
    {
        $value = $proceed($key, $index);
        if ($key !== $this->optionMeta->getName() || !$this->isOptionEnabled($subject)) {
            return $value;
        }
        return $this->getOptionValue($subject, $value);
    }
    
    /**
     * Check if option is enabled
     * 
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    private function isOptionEnabled(\Magento\Catalog\Model\Product $product): bool
    {
        if (
            $product->getId() === null || 
            !$this->optionConfig->isEnabled() || 
            $this->isSourceItemManagementAllowedForProductType->execute($product->getTypeId()) === false
        ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get options
     * 
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return array
     */
    private function getOptions(\Magento\Catalog\Api\Data\ProductInterface $product): array
    {
        $optionsKey = 'source_'.$this->optionMeta->getName().'_object';
        $options = $product->getData($optionsKey);
        if ($options !== null) {
            return $options;
        }
        $sku = $product->getData(\Magento\Catalog\Api\Data\ProductInterface::SKU);
        $storeId = (int) $this->storeManager->getStore($product->getStoreId())->getId();
        $sources = $this->getCurrentSources->execute($storeId);
        $product->setData($optionsKey, $this->getOptions->execute([$sku], array_keys($sources))[$sku] ?? []);
        return $product->getData($optionsKey);
    }

    /**
     * Get option
     * 
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface|null
     */
    private function getOption(\Magento\Catalog\Api\Data\ProductInterface $product): ?\Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface
    {
        $sourceCode = $product->getSourceCode();
        if (empty($sourceCode)) {
            return null;
        }
        $options = $this->getOptions($product);
        return $options[$sourceCode] ?? null;
    }

    /**
     * Get option value
     * 
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param mixed $defaultValue
     * @return mixed
     */
    private function getOptionValue(\Magento\Catalog\Api\Data\ProductInterface $product, $defaultValue)
    {
        $option = $this->getOption($product);
        if ($option !== null && $option->getValue() !== null) {
            return $option->getValue();
        }
        return $defaultValue;
    }
}