<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\ResourceModel\Quote\Address;

/**
 * Quote address collection wrapper
 */
class Collection extends \Ecombricks\Common\DataObject\Wrapper
{
    /**
     * Wrapper factory
     * 
     * @var \Ecombricks\Common\DataObject\WrapperFactory
     */
    private $wrapperFactory;

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
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory,
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory
    )
    {
        parent::__construct($objectReflectionFactory);
        $this->wrapperFactory = $wrapperFactory;
        $this->quoteAddressWrapperFactory = $quoteAddressWrapperFactory;
    }

    /**
     * After load with filter
     *
     * @return void
     */
    public function afterLoadWithFilter(): void
    {
        $collection = $this->getObject();
        $quoteAddressIds = [];
        foreach ($collection->getItems() as $quoteAddress) {
            $quoteAddressIds[] = $quoteAddress->getId();
        }
        $shippingMethodWrapper = $this->wrapperFactory->create(
            $this->getObject(),
            \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\ResourceModel\Quote\Address\Collection\ShippingMethod::class
        );
        $sourceOptions = $shippingMethodWrapper->getSourceOptions($quoteAddressIds);
        foreach ($collection->getItems() as $quoteAddress) {
            $quoteAddressId = $quoteAddress->getId();
            $this->quoteAddressWrapperFactory->create($quoteAddress)->setShippingMethods($sourceOptions[$quoteAddressId] ?? []);
        }
    }
}