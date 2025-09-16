<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Ui\Catalog\DataProvider\Product\Form\Modifier\SourceItem;

/**
 * Source item price product form modifier
 */
class Price extends \Ecombricks\InventoryCommon\Ui\DataProvider\Product\Form\Modifier\SourceItem\Option
{
    /**
     * Locale currency
     * 
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    private $localeCurrency;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\Ui\DataProvider\Modifier\Meta $meta
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Get $getOptions
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Meta $optionMeta
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Config $optionConfig
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Catalog\Model\Locator\LocatorInterface $locator
     * @param \Magento\Framework\Stdlib\ArrayManager $arrayManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface $isSingleSourceMode
     * @param \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param int $sortOrder
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\Ui\DataProvider\Modifier\Meta $meta,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Get $getOptions,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Meta $optionMeta,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Config $optionConfig,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Catalog\Model\Locator\LocatorInterface $locator,
        \Magento\Framework\Stdlib\ArrayManager $arrayManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface $isSingleSourceMode,
        \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        int $sortOrder = 53
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
        $this->localeCurrency = $localeCurrency;
    }
    
    /**
     * Get default option value
     * 
     * @return string|null
     */
    protected function getDefaultOptionValue(): ?string
    {
        return $this->formatValue((string) $this->locator->getProduct()->getPrice());
    }
    
    /**
     * Format value
     * 
     * @param string $value
     * @return string
     */
    protected function formatValue(string $value): string
    {
        return (string) $this->localeCurrency->getCurrency($this->storeManager->getStore()->getBaseCurrencyCode())
            ->toCurrency((float) $value, ['display' => \Magento\Framework\Currency::NO_SYMBOL]);
    }
}