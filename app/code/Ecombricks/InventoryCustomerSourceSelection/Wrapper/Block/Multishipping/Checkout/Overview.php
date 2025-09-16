<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Block\Multishipping\Checkout;

/**
 * Multi-shipping checkout overview block wrapper
 */
class Overview extends \Ecombricks\Common\DataObject\Wrapper implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
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
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory
     * @param \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode $getSourceBySourceCode
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory,
        \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode $getSourceBySourceCode,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory
    )
    {
        parent::__construct($objectReflectionFactory);
        $this->getSourceBySourceCode = $getSourceBySourceCode;
        $this->quoteAddressWrapperFactory = $quoteAddressWrapperFactory;
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
     * Get current shipping rates
     * 
     * @param \Magento\Quote\Api\Data\AddressInterface $quoteAddress
     * @return \Magento\Quote\Model\Quote\Address\Rate[]
     */
    public function getCurrentShippingRates(\Magento\Quote\Api\Data\AddressInterface $quoteAddress): array
    {
        return $this->quoteAddressWrapperFactory->create($quoteAddress)->getCurrentShippingRates();
    }
}