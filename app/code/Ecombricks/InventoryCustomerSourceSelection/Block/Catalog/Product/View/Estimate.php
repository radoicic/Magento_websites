<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Block\Catalog\Product\View;

/**
 * Product estimate block
 */
class Estimate extends \Magento\Catalog\Block\Product\View
{
    /**
     * Get current salable source items
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetCurrentSalableSourceItems
     */
    private $getCurrentSalableSourceItems;

    /**
     * Configuration provider
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product\Checkout\CompositeConfigProvider
     */
    private $configProvider;

    /**
     * Configuration
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product\SourceQuote\Config 
     */
    private $config;
    
    /**
     * Layout processors
     * 
     * @var array|\Magento\Checkout\Block\Checkout\LayoutProcessorInterface[]
     */
    private $layoutProcessors;

    /**
     * Constructor
     * 
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetCurrentSalableSourceItems $getCurrentSalableSourceItems
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product\Checkout\CompositeConfigProvider $configProvider
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product\SourceQuote\Config $config
     * @param array $layoutProcessors
     * @param array $data
     * @return void
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetCurrentSalableSourceItems $getCurrentSalableSourceItems,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product\Checkout\CompositeConfigProvider $configProvider,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product\SourceQuote\Config $config,
        array $layoutProcessors = [],
        array $data = []
    )
    {
        $this->getCurrentSalableSourceItems = $getCurrentSalableSourceItems;
        $this->configProvider = $configProvider;
        $this->config = $config;
        $this->layoutProcessors = $layoutProcessors;
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
    }
    
    /**
     * Check if is enabled
     * 
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isEnabled();
    }

    /**
     * Check if has sources
     * 
     * @return bool
     */
    public function hasSources(): bool
    {
        $sourceCodes = array_keys($this->getCurrentSalableSourceItems->execute($this->getProduct()->getData(\Magento\Catalog\Api\Data\ProductInterface::SKU)));
        return count($sourceCodes) ? true : false;
    }

    /**
     * Get JS layout
     *
     * @return string
     */
    public function getJsLayout(): string
    {
        foreach ($this->layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout);
        }
        return (string) json_encode($this->jsLayout, JSON_HEX_TAG);
    }

    /**
     * Get base URL
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * Get checkout configuration
     *
     * @return array
     */
    public function getCheckoutConfig(): array
    {
        return $this->configProvider->getConfig();
    }

    /**
     * Get serialized checkout configuration
     * 
     * @return string
     */
    public function getSerializedCheckoutConfig(): string
    {
        return (string) json_encode($this->getCheckoutConfig(), JSON_HEX_TAG);
    }
}