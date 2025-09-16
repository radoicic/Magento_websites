<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product\Checkout;

/**
 * Default product checkout configuration provider
 */
class DefaultConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /**
     * Product
     * 
     * @var \Magento\Catalog\Model\Product 
     */
    protected $product;
    
    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    
    /**
     * Locale format
     * 
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $localeFormat;
    
    /**
     * Post code config
     * 
     * @var \Magento\Directory\Model\Country\Postcode\ConfigInterface
     */
    protected $postCodeConfig;
    
    /**
     * Scope config
     * 
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * Shipping config
     * 
     * @var \Magento\Shipping\Model\Config
     */
    protected $shippingConfig;
    
    /**
     * Get source by source code
     * 
     * @var \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode
     */
    private $getSourceBySourceCode;
    
    /**
     * Get current salable source items
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetCurrentSalableSourceItems
     */
    private $getCurrentSalableSourceItems;
    
    /**
     * Sources
     * 
     * @var array
     */
    protected $sources;
    
    /**
     * Constructor
     * 
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Directory\Model\Country\Postcode\ConfigInterface $postCodeConfig
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode $getSourceBySourceCode
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetCurrentSalableSourceItems $getCurrentSalableSourceItems
     * @return void
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Directory\Model\Country\Postcode\ConfigInterface $postCodeConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode $getSourceBySourceCode,
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetCurrentSalableSourceItems $getCurrentSalableSourceItems
    )
    {
        $this->registry = $registry;
        $this->localeFormat = $localeFormat;
        $this->postCodeConfig = $postCodeConfig;
        $this->scopeConfig = $scopeConfig;
        $this->shippingConfig = $shippingConfig;
        $this->getSourceBySourceCode = $getSourceBySourceCode;
        $this->getCurrentSalableSourceItems = $getCurrentSalableSourceItems;
    }

    /**
     * Get configuration
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfig()
    {
        $store = $this->getStore();
        $output = [];
        $output['quoteData'] = $this->getQuoteData();
        $output['productData'] = $this->getProductData();
        $output['quoteItemData'] = [];
        $output['storeCode'] = $store->getCode();
        $output['priceFormat'] = $this->localeFormat->getPriceFormat(null, $store->getCurrentCurrency()->getCode());
        $output['basePriceFormat'] = $this->localeFormat->getPriceFormat(null, $store->getBaseCurrency()->getCode());
        $output['postCodes'] = $this->postCodeConfig->getPostCodes();
        $output['totalsData'] = [];
        $output['activeCarriers'] = $this->getActiveCarriers();
        $output['originCountryCode'] = $this->getOriginCountryCode();
        return $output;
    }
    
    /**
     * Get product
     * 
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    private function getProduct(): \Magento\Catalog\Api\Data\ProductInterface
    {
        if ($this->product !== null) {
            return $this->product;
        }
        return $this->product = $this->registry->registry('product');
    }

    /**
     * Get store
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    private function getStore(): \Magento\Store\Api\Data\StoreInterface
    {
        return $this->getProduct()->getStore();
    }

    /**
     * Get quote data
     *
     * @return array
     */
    private function getQuoteData(): array
    {
        $product = $this->getProduct();
        return [
            'is_virtual' => $product->isVirtual(),
        ];
    }

    /**
     * Get sources
     * 
     * @return array
     */
    private function getSources(): array
    {
        if ($this->sources !== null) {
            return $this->sources;
        }
        $this->sources = [];
        $product = $this->getProduct();
        $sourceCodes = array_keys($this->getCurrentSalableSourceItems->execute($product->getData(\Magento\Catalog\Api\Data\ProductInterface::SKU)));
        foreach ($sourceCodes as $sourceCode) {
            $source = $this->getSourceBySourceCode->execute((string) $sourceCode);
            $this->sources[] = [
                'source_code' => $source->getSourceCode(),
                'name' => $source->getName(),
            ];
        }
        return $this->sources;
    }

    /**
     * Get product data
     * 
     * @return array
     */
    private function getProductData(): array
    {
        $product = $this->getProduct();
        return [
            'sku' => $product->getData(\Magento\Catalog\Api\Data\ProductInterface::SKU),
            'is_virtual' => $product->isVirtual(),
            'sources' => $this->getSources(),
        ];
    }
    
    /**
     * Get active carriers
     *
     * @return array
     */
    private function getActiveCarriers(): array
    {
        $activeCarriers = [];
        foreach ($this->shippingConfig->getActiveCarriers() as $carrier) {
            $activeCarriers[] = $carrier->getCarrierCode();
        }
        return $activeCarriers;
    }
    
    /**
     * Get origin country code
     * 
     * @return string
     */
    private function getOriginCountryCode(): string
    {
        return $this->scopeConfig->getValue(
            \Magento\Shipping\Model\Config::XML_PATH_ORIGIN_COUNTRY_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()
        );
    }
}