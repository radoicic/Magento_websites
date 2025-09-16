<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\ResourceModel\Order\Item;

/**
 * Order item collection wrapper
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
     * Order item wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory 
     */
    private $orderItemWrapperFactory;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory $orderItemWrapperFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory,
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory $orderItemWrapperFactory
    )
    {
        parent::__construct($objectReflectionFactory);
        $this->wrapperFactory = $wrapperFactory;
        $this->orderItemWrapperFactory = $orderItemWrapperFactory;
    }

    /**
     * After load with filter
     *
     * @return void
     */
    public function afterLoadWithFilter(): void
    {
        $collection = $this->getObject();
        $orderItemIds = [];
        foreach ($collection->getItems() as $orderItem) {
            $orderItemIds[] = $orderItem->getId();
        }
        $collectionSourceWrapper = $this->wrapperFactory->create(
            $this->getObject(),
            \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\ResourceModel\Order\Item\Collection\Source::class
        );
        $sourceCodes = $collectionSourceWrapper->getSourceCodes($orderItemIds);
        foreach ($collection->getItems() as $orderItem) {
            $orderItemId = $orderItem->getId();
            $this->orderItemWrapperFactory->create($orderItem)->setSourceCode($sourceCodes[$orderItemId] ?? null);
        }
    }
}