<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order;

/**
 * Order item wrapper factory 
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
     * @param \Magento\Sales\Api\Data\OrderItemInterface $orderItem
     * @return \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\Item
     */
    public function create(
        \Magento\Sales\Api\Data\OrderItemInterface $orderItem
    ): \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\Item
    {
        return $this->wrapperFactory->create(
            $orderItem,
            \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\Item::class
        );
    }
}