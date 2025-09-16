<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Ui\DataProvider\Product\Form\Modifier\SourceItem;

/**
 * Source item option product form modifier
 */
class Option extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    /**
     * Meta
     * 
     * @var \Ecombricks\Common\Ui\DataProvider\Modifier\Meta
     */
    private $meta;

    /**
     * Get source item options
     * 
     * @var \Ecombricks\InventoryCommon\Api\SourceItem\Option\GetInterface
     */
    private $getOptions;

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
    protected $optionConfig;

    /**
     * Stock configuration
     * 
     * @var \Magento\CatalogInventory\Api\StockConfigurationInterface 
     */
    private $stockConfiguration;

    /**
     * Locator
     * 
     * @var \Magento\Catalog\Model\Locator\LocatorInterface
     */
    protected $locator;

    /**
     * Array manager
     * 
     * @var \Magento\Catalog\Model\Locator\LocatorInterface
     */
    protected $arrayManager;

    /**
     * Store manager
     * 
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Is single source mode
     * 
     * @var \Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface 
     */
    private $isSingleSourceMode;

    /**
     * Is source item management allowed for product type
     * 
     * @var \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface
     */
    private $isSourceItemManagementAllowedForProductType;

    /**
     * Sort order
     * 
     * @var int
     */
    private $sortOrder;
    
    /**
     * Is stock configuration
     * 
     * @var bool
     */
    private $isStockConfig;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\Ui\DataProvider\Modifier\Meta $meta
     * @param \Ecombricks\InventoryCommon\Api\SourceItem\Option\GetInterface $getOptions
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta $optionMeta
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config $optionConfig
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Catalog\Model\Locator\LocatorInterface $locator
     * @param \Magento\Framework\Stdlib\ArrayManager $arrayManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface $isSingleSourceMode
     * @param \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param int $sortOrder
     * @param bool $isStockConfig
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\Ui\DataProvider\Modifier\Meta $meta,
        \Ecombricks\InventoryCommon\Api\SourceItem\Option\GetInterface $getOptions,
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta $optionMeta,
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config $optionConfig,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Catalog\Model\Locator\LocatorInterface $locator,
        \Magento\Framework\Stdlib\ArrayManager $arrayManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface $isSingleSourceMode,
        \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        int $sortOrder,
        bool $isStockConfig = false
    )
    {
        $this->meta = $meta;
        $this->getOptions = $getOptions;
        $this->optionMeta = $optionMeta;
        $this->optionConfig = $optionConfig;
        $this->stockConfiguration = $stockConfiguration;
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->storeManager = $storeManager;
        $this->isSingleSourceMode = $isSingleSourceMode;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->sortOrder = $sortOrder;
        $this->isStockConfig = $isStockConfig;
    }
    
    /**
     * Modify data
     * 
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        if (!$this->optionConfig->isEnabled()) {
            return $data;
        }
        $product = $this->locator->getProduct();
        $productId = $product->getId();
        $assignedSources = $data[$productId]['sources']['assigned_sources'] ?? null;
        if (
            $productId === null || 
            $this->isSourceItemManagementAllowedForProductType->execute($product->getTypeId()) === false || 
            $assignedSources === null
        ) {
            return $data;
        }
        $data[$productId]['sources']['assigned_sources'] = $this->prepareAssignedSourcesData($assignedSources);
        return $data;
    }
    
    /**
     * Modify meta
     * 
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->optionConfig->isEnabled() || $this->isSingleSourceMode->execute()) {
            return $meta;
        }
        $optionName = $this->optionMeta->getName();
        $this->meta->set($meta);
        $this->meta->set(
            $this->meta->createContainer(
                [
                    'label' => $this->optionMeta->getLabel(),
                    'showLabel' => false,
                    'component' => 'Magento_Ui/js/form/components/group',
                    'additionalClasses' => 'admin__field-container_'.$optionName,
                    'sortOrder' => $this->sortOrder,
                ],
                [
                    $optionName => $this->meta->createField(
                        array_merge(
                            [
                                'formElement' => \Magento\Ui\Component\Form\Element\Input::NAME,
                                'labelVisible' => false,
                                'dataScope' => $optionName,
                                'additionalClasses' => 'admin__field-'.$optionName,
                                'default' => $this->getDefaultOptionValue(),
                                'sortOrder' => 10,
                            ],
                            $this->getValueFieldMeta()
                        )
                    ),
                    $optionName.'_use_default' => $this->meta->createField(
                        [
                            'component' => 'Ecombricks_InventoryCommon/js/product/form/source-item/option/use-default',
                            'labelVisible' => false,
                            'dataType' => \Magento\Ui\Component\Form\Element\DataType\Text::NAME,
                            'formElement' => \Magento\Ui\Component\Form\Element\Checkbox::NAME,
                            'dataScope' => $optionName.'_use_default',
                            'description' => __('Use Default'),
                            'optionName' => $optionName,
                            'isStockConfig' => $this->isStockConfig ? '1' : '0',
                            'default' => '1',
                            'valueMap' => [
                                'false' => '0',
                                'true' => '1',
                            ],
                            'sortOrder' => 20,
                        ]
                    ),
                ]
            ),
            'sources/children/assigned_sources/children/record/children/container_'.$optionName
        );
        if (!$this->isStockConfig) {
            return $this->meta->get();
        }
        $stockDataPath = $this->meta->findPath('stock_data', null, 'children');
        if ($stockDataPath !== null) {
            $this->meta->set(
                [
                    'visible' => 0,
                    'imports' => '',
                ],
                $stockDataPath.'/children/container_'.$optionName.'/arguments/data/config'
            );
        }
        return $this->meta->get();
    }
    
    /**
     * Get default option value
     * 
     * @return string|null
     */
    protected function getDefaultOptionValue(): ?string
    {
        return $this->isStockConfig ? (string) $this->stockConfiguration->getDefaultConfigValue($this->optionMeta->getName()) : null;
    }
    
    /**
     * Format value
     * 
     * @param string $value
     * @return string
     */
    protected function formatValue(string $value): string
    {
        return (string) (float) $value;
    }

    /**
     * Get value field meta
     * 
     * @return array
     */
    protected function getValueFieldMeta(): array
    {
        return [];
    }

    /**
     * Get SKU
     * 
     * @return string
     */
    private function getSku(): string
    {
        return $this->locator->getProduct()->getData(\Magento\Catalog\Api\Data\ProductInterface::SKU);
    }
    
    /**
     * Get source codes
     * 
     * @param array $assignedSources
     * @return array
     */
    private function getSourceCodes(array $assignedSources): array
    {
        $sourceCodes = [];
        foreach ($assignedSources as &$source) {
            $sourceCodes[] = $source[\Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::SOURCE_CODE];
        }
        return $sourceCodes;
    }
    
    /**
     * Get source item options
     * 
     * @param array $assignedSources
     * @return array
     */
    private function getOptions(array $assignedSources): array
    {
        $sourceCodes = $this->getSourceCodes($assignedSources);
        if (empty($sourceCodes)) {
            return [];
        }
        return $this->getOptions->execute([$this->getSku()], $sourceCodes);
    }
    
    /**
     * Prepare assigned sources data
     * 
     * @param array $assignedSources
     * @return array
     */
    private function prepareAssignedSourcesData(array $assignedSources): array
    {
        $optionName = $this->optionMeta->getName();
        $sku = $this->getSku();
        $defaultOptionValue = $this->getDefaultOptionValue();
        $options = $this->getOptions($assignedSources);
        foreach ($assignedSources as &$source) {
            $sourceCode = $source[\Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::SOURCE_CODE];
            $option = $options[$sku][$sourceCode] ?? null;
            if ($option !== null && $option->getValue() !== null) {
                $source[$optionName] = $this->formatValue((string) $option->getValue());
                $source[$optionName.'_use_default'] = '0';
            } else {
                $source[$optionName] = $this->formatValue((string) $defaultOptionValue);
                $source[$optionName.'_use_default'] = '1';
            }
        }
        return $assignedSources;
    }
}