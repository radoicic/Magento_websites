<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Block\Sales\Adminhtml\Order\Create\Shipping\Method;

/**
 * Create order shipping method form wrapper
 */
class Form extends \Ecombricks\Common\DataObject\Wrapper implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * Current source provider
     * 
     * @var \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface
     */
    private $currentSourceProvider;

    /**
     * Get source by source code
     * 
     * @var \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode
     */
    private $getSourceBySourceCode;
    
    /**
     * Quote address wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory
     */
    private $quoteAddressWrapperFactory;

    /**
     * Scope configuration
     * 
     * @var \Magento\Framework\App\Config\ScopeConfigInterface 
     */
    private $scopeConfig;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory
     * @param \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface $currentSourceProvider
     * @param \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode $getSourceBySourceCode
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory,
        \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface $currentSourceProvider,
        \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode $getSourceBySourceCode,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        parent::__construct($objectReflectionFactory);
        $this->currentSourceProvider = $currentSourceProvider;
        $this->getSourceBySourceCode = $getSourceBySourceCode;
        $this->quoteAddressWrapperFactory = $quoteAddressWrapperFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get source code
     * 
     * @param string $sourceCode
     * @return string
     */
    public function getSourceName(string $sourceCode): string
    {
        $source = $this->getSourceBySourceCode->execute($sourceCode);
        return $source ? $source->getName() : $sourceCode;
    }

    /**
     * Get source carrier name
     * 
     * @param string $sourceCode
     * @param string $carrierCode
     * @return string
     */
    public function getSourceCarrierName(string $sourceCode, string $carrierCode): string
    {
        $originSourceCode = $this->currentSourceProvider->getSourceCode();
        $this->currentSourceProvider->setSourceCode($sourceCode);
        $carrierTitle = $this->scopeConfig->getValue('carriers/'.$carrierCode.'/title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->currentSourceProvider->setSourceCode($originSourceCode);
        if ($carrierTitle) {
            return $carrierTitle;
        }
        return $carrierCode;
    }

    /**
     * Get active shipping rates
     *
     * @return \Magento\Quote\Model\Quote\Address\Rate[]
     */
    public function getActiveShippingRates(): array
    {
        $address = $this->getObject()->getAddress();
        $shippingMethods = $address->getShippingMethod();
        if (empty($shippingMethods)) {
            return [];
        }
        $groupedShippingRates = $address->getGroupedAllShippingRates();
        if (empty($groupedShippingRates)) {
            return [];
        }
        $shippingRates = [];
        foreach ($groupedShippingRates as $sourceCode => $sourceShippingRates) {
            foreach ($sourceShippingRates as $carrierShippingRates) {
                foreach ($carrierShippingRates as $shippingRate) {
                    if ($shippingRate->getCode() !== $shippingMethods[$sourceCode]) {
                        continue;
                    }
                    $shippingRates[$sourceCode] = $shippingRate;
                    break;
                }
            }
        }
        return $shippingRates;
    }

    /**
     * Get source codes JSON
     * 
     * @return string
     */
    public function getSourceCodesJson(): string
    {
        return json_encode($this->quoteAddressWrapperFactory->create($this->getObject()->getAddress())->getSourceCodes(), JSON_HEX_TAG);
    }

    /**
     * Get shipping methods JSON
     * 
     * @return string
     */
    public function getShippingMethodsJson(): string
    {
        return json_encode($this->quoteAddressWrapperFactory->create($this->getObject()->getAddress())->getShippingMethods(), JSON_HEX_TAG | JSON_FORCE_OBJECT);
    }
    
    /**
     * Get source shipping rate by code
     * 
     * @param \Magento\Quote\Api\Data\AddressInterface $quoteAddress
     * @param string $sourceCode
     * @param string $code
     * @return \Magento\Quote\Model\Quote\Address\Rate|null
     */
    public function getShippingRateByCode(
        \Magento\Quote\Api\Data\AddressInterface $quoteAddress,
        string $sourceCode,
        string $code
    ): ?\Magento\Quote\Model\Quote\Address\Rate
    {
        return $this->quoteAddressWrapperFactory->create($quoteAddress)->getShippingRateByCode($sourceCode, $code);
    }    

    /**
     * Get shipping method
     * 
     * @param \Magento\Quote\Api\Data\AddressInterface $quoteAddress
     * @param string $sourceCode
     * @return string|null
     */
    public function getShippingMethod(
        \Magento\Quote\Api\Data\AddressInterface $quoteAddress,
        string $sourceCode
    ): ?string
    {
        return $this->quoteAddressWrapperFactory->create($quoteAddress)->getShippingMethod($sourceCode);
    }
}