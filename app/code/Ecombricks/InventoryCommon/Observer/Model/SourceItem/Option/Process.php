<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Observer\Model\SourceItem\Option;

/**
 * Process source item options
 */
class Process implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Is source item management allowed for product type
     * 
     * @var \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface
     */
    private $isSourceItemManagementAllowedForProductType;

    /**
     * Is single source mode
     * 
     * @var \Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface
     */
    private $isSingleSourceMode;

    /**
     * Default source provider
     * 
     * @var \Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface
     */
    private $defaultSourceProvider;

    /**
     * Source item options processor
     * 
     * @var \Ecombricks\InventoryCommon\Model\SourceItem\Option\Processor
     */
    private $optionsProcessor;

    /**
     * Source item option meta
     * 
     * @var \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta
     */
    private $optionMeta;

    /**
     * Source item option configuration
     * 
     * @var \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config
     */
    private $optionConfig;

    /**
     * Constructor
     * 
     * @param \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param \Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface $isSingleSourceMode
     * @param \Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface $defaultSourceProvider
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Processor $optionsProcessor
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta $optionMeta
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config $optionConfig
     * @return void
     */
    public function __construct(
        \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        \Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface $isSingleSourceMode,
        \Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface $defaultSourceProvider,
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Processor $optionsProcessor,
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta $optionMeta,
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config $optionConfig
    )
    {
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->isSingleSourceMode = $isSingleSourceMode;
        $this->defaultSourceProvider = $defaultSourceProvider;
        $this->optionsProcessor = $optionsProcessor;
        $this->optionMeta = $optionMeta;
        $this->optionConfig = $optionConfig;
    }

    /**
     * Execute
     * 
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->optionConfig->isEnabled()) {
            return $this;
        }
        $event = $observer->getEvent();
        $product = $event->getProduct();
        if ($this->isSourceItemManagementAllowedForProductType->execute($product->getTypeId()) === false) {
            return $this;
        }
        $request = $event->getController()->getRequest();
        $optionsData = [];
        if ($this->isSingleSourceMode->execute()) {
            $optionName = $this->optionMeta->getName();
            $stockData = $request->getParam('product', [])['stock_data'] ?? [];
            $optionsData[] = [
                \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::SOURCE_CODE => $this->defaultSourceProvider->getCode(),
                $optionName => $stockData[$optionName] ?? 0,
                $optionName.'_use_default' => $stockData['use_config_'.$optionName] ?? 1,
            ];
        } else {
            $sources = $request->getParam('sources', []);
            $stockData = $request->getParam('product', [])['stock_data'] ?? [];
            if (isset($sources['assigned_sources']) && is_array($sources['assigned_sources'])) {
                $optionsData = $this->castOptionsData($sources['assigned_sources'], $stockData);
            }
        }
        $this->optionsProcessor->execute($product->getData(\Magento\Catalog\Api\Data\ProductInterface::SKU), $optionsData);
        return $this;
    }

    /**
     * Cast options data
     * 
     * @param array $optionsData
     * @param array $stockData
     * @return array
     */
    private function castOptionsData(array $optionsData, array $stockData): array
    {
        $optionName = $this->optionMeta->getName();
        foreach ($optionsData as &$optionData) {
            if (!array_key_exists('quantity', $optionData) && isset($optionData['qty'])) {
                $optionData['quantity'] = (int) $optionData['qty'];
            }
            if (!array_key_exists('status', $optionData) && isset($optionData['source_status'])) {
                $optionData['source_status']= (int) $optionData['source_status'];
            }
            if (!array_key_exists($optionName, $optionData)|| $optionData[$optionName] == null) {
                $optionData[$optionName] = $stockData[$optionName] ?? 0;
            }
            if (!array_key_exists($optionName.'_use_default', $optionData) || $optionData[$optionName.'_use_default'] == null) {
                $optionData[$optionName.'_use_default'] = $stockData['use_config_'.$optionName] ?? 1;
            }
        }
        return $optionsData;
    }
}