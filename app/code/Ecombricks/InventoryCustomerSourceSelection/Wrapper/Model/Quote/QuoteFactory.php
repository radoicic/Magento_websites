<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote;

/**
 * Quote item wrapper factory
 */
class QuoteFactory
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
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote
     */
    public function create(
        \Magento\Quote\Api\Data\CartInterface $quote
    ): \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote
    {
        return $this->wrapperFactory->create(
            $quote,
            \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote::class
        );
    }
}