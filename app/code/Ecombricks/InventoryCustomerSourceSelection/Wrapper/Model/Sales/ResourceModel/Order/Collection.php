<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\ResourceModel\Order;

/**
 * Order collection wrapper
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
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory,
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
    )
    {
        parent::__construct($objectReflectionFactory);
        $this->wrapperFactory = $wrapperFactory;
    }

    /**
     * After load with filter
     *
     * @return void
     */
    public function afterLoadWithFilter(): void
    {
        $collection = $this->getObject();
        $orderIds = [];
        foreach ($collection->getItems() as $order) {
            $orderIds[] = $order->getId();
        }
        $collectionShippingMethodWrapper = $this->wrapperFactory->create(
            $this->getObject(),
            \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\ResourceModel\Order\Collection\ShippingMethod::class
        );
        $sourceOptions = $collectionShippingMethodWrapper->getSourceOptions($orderIds);
        foreach ($collection->getItems() as $order) {
            $orderId = $order->getId();
            $orderWrapper = $this->wrapperFactory->create(
                $order,
                \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order::class
            );
            $orderWrapper->setShippingMethods($sourceOptions[$orderId] ?? []);
        }
    }
}