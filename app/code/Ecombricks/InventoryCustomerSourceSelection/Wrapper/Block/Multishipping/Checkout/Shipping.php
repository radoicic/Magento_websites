<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Block\Multishipping\Checkout;

/**
 * Multi-shipping checkout shipping block wrapper
 */
class Shipping extends \Ecombricks\Common\DataObject\Wrapper implements \Magento\Framework\View\Element\Block\ArgumentInterface
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
        $this->scopeConfig->setSourceCode($sourceCode);
        $carrierTitle = $this->scopeConfig->getValue('carriers/'.$carrierCode.'/title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->scopeConfig->setSourceCode($originSourceCode);
        if ($carrierTitle) {
            return $carrierTitle;
        }
        return $carrierCode;
    }

    /**
     * Get source codes
     * 
     * @param \Magento\Quote\Api\Data\AddressInterface $quoteAddress
     * @return array
     */
    public function getSourceCodes(\Magento\Quote\Api\Data\AddressInterface $quoteAddress): array
    {
        return $this->quoteAddressWrapperFactory->create($quoteAddress)->getSourceCodes();
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