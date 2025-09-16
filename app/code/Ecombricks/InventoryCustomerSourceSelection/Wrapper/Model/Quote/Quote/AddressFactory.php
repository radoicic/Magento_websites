<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote;

/**
 * Quote address wrapper factory
 */
class AddressFactory
{
    /**
     * Wrapper factory
     * 
     * @var \Ecombricks\Common\DataObject\WrapperFactory
     */
    private $wrapperFactory;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @return void
     */
    public function __construct(\Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory)
    {
        $this->wrapperFactory = $wrapperFactory;
    }

    /**
     * Create
     * 
     * @param \Magento\Quote\Api\Data\AddressInterface $quoteAddress
     * @return \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\Address
     */
    public function create(
        \Magento\Quote\Api\Data\AddressInterface $quoteAddress
    ): \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\Address
    {
        return $this->wrapperFactory->create(
            $quoteAddress,
            \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\Address::class
        );
    }
}