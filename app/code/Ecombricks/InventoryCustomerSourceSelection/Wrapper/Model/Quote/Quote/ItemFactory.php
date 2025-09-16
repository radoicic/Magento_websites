<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote;

/**
 * Quote item wrapper factory
 */
class ItemFactory
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
     * @param \Magento\Quote\Api\Data\CartItemInterface $quoteItem
     * @return \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\Item
     */
    public function create(
        \Magento\Quote\Api\Data\CartItemInterface $quoteItem
    ): \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\Item
    {
        return $this->wrapperFactory->create(
            $quoteItem,
            \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\Item::class
        );
    }
}