<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Block\Catalog\Product\View;

/**
 * Product source block
 */
class Source extends \Magento\Catalog\Block\Product\View
{
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
     * @var \Magento\InventoryApi\Api\Data\SourceInterface[]
     */
    private $sources;

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
     * @param \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode $getSourceBySourceCode
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetCurrentSalableSourceItems $getCurrentSalableSourceItems
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
        \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode $getSourceBySourceCode,
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetCurrentSalableSourceItems $getCurrentSalableSourceItems,
        array $data = []
    )
    {
        $this->getSourceBySourceCode = $getSourceBySourceCode;
        $this->getCurrentSalableSourceItems = $getCurrentSalableSourceItems;
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
     * Get sources
     * 
     * @return array
     */
    public function getSources(): array
    {
        if ($this->sources !== null) {
            return $this->sources;
        }
        $this->sources = [];
        $product = $this->getProduct();
        $sourceCodes = array_keys($this->getCurrentSalableSourceItems->execute($product->getData(\Magento\Catalog\Api\Data\ProductInterface::SKU)));
        foreach ($sourceCodes as $sourceCode) {
            $this->sources[$sourceCode] = $this->getSourceBySourceCode->execute((string) $sourceCode);
        }
        return $this->sources;
    }

    /**
     * Get current source code
     * 
     * @return string
     */
    public function getCurrentSourceCode(): string
    {
        $sourceCode = (string) $this->getProduct()->getPreconfiguredValues()->getSource();
        return $sourceCode ? $sourceCode : $this->getDefaultSourceCode();
    }

    /**
     * Get source validators
     *
     * @return array
     */
    public function getSourceValidators(): array
    {
        $validators = [];
        $validators['required-number'] = true;
        return $validators;
    }

    /**
     * Get default source code
     * 
     * @return string|null
     */
    private function getDefaultSourceCode(): ?string
    {
        $sourceCodes = array_keys($this->getSources());
        return !empty($sourceCodes) ? (string) current($sourceCodes) : null;
    }
}