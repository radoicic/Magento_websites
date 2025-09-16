<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Ui\Catalog\DataProvider\Product\Form\Modifier\SourceItem;

/**
 * Source item tax class product form modifier
 */
class TaxClass extends \Ecombricks\InventoryCommon\Ui\DataProvider\Product\Form\Modifier\SourceItem\Option
{
    /**
     * Product tax class source
     * 
     * @var \Magento\Tax\Model\TaxClass\Source\Product
     */
    private $productTaxClassSource;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\Ui\DataProvider\Modifier\Meta $meta
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Get $getOptions
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Meta $optionMeta
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Config $optionConfig
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Catalog\Model\Locator\LocatorInterface $locator
     * @param \Magento\Framework\Stdlib\ArrayManager $arrayManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface $isSingleSourceMode
     * @param \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param \Magento\Tax\Model\TaxClass\Source\Product $productTaxClassSource
     * @param int $sortOrder
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\Ui\DataProvider\Modifier\Meta $meta,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Get $getOptions,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Meta $optionMeta,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Config $optionConfig,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Catalog\Model\Locator\LocatorInterface $locator,
        \Magento\Framework\Stdlib\ArrayManager $arrayManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface $isSingleSourceMode,
        \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        \Magento\Tax\Model\TaxClass\Source\Product $productTaxClassSource,
        int $sortOrder = 54
    )
    {
        parent::__construct(
            $meta,
            $getOptions,
            $optionMeta,
            $optionConfig,
            $stockConfiguration,
            $locator,
            $arrayManager,
            $storeManager,
            $isSingleSourceMode,
            $isSourceItemManagementAllowedForProductType,
            $sortOrder,
            false
        );
        $this->productTaxClassSource = $productTaxClassSource;
    }
    
    /**
     * Get default option value
     * 
     * @return string|null
     */
    protected function getDefaultOptionValue(): ?string
    {
        return (string) $this->locator->getProduct()->getTaxClassId();
    }
    
    /**
     * Format value
     * 
     * @param string $value
     * @return string
     */
    protected function formatValue(string $value): string
    {
        return (string) (int) $value;
    }
    
    /**
     * Get value field meta
     * 
     * @return array
     */
    protected function getValueFieldMeta(): array
    {
        return [
            'formElement' => \Magento\Ui\Component\Form\Element\Select::NAME,
            'options' => $this->getValueOptions(),
        ];
    }
    
    /**
     * Get value options
     * 
     * @return array
     */
    private function getValueOptions(): array
    {
        return $this->productTaxClassSource->toOptionArray();
    }
}