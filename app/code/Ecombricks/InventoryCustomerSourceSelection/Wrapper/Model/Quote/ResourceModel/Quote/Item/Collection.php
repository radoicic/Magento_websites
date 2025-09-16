<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\ResourceModel\Quote\Item;

/**
 * Quote item collection wrapper
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
     * Quote item wrapper factory
     * 
     * @var \Ecombricks\Common\DataObject\WrapperFactory
     */
    private $quoteItemWrapperFactory;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory $quoteItemWrapperFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory,
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory $quoteItemWrapperFactory
    )
    {
        parent::__construct($objectReflectionFactory);
        $this->wrapperFactory = $wrapperFactory;
        $this->quoteItemWrapperFactory = $quoteItemWrapperFactory;
    }

    /**
     * After load with filter
     *
     * @return void
     */
    public function afterLoadWithFilter(): void
    {
        $collection = $this->getObject();
        $quoteItemIds = [];
        foreach ($collection->getItems() as $quoteItem) {
            $quoteItemIds[] = $quoteItem->getId();
        }
        $collectionSourceWrapper = $this->wrapperFactory->create(
            $this->getObject(),
            \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\ResourceModel\Quote\Item\Collection\Source::class
        );
        $sourceCodes = $collectionSourceWrapper->getSourceCodes($quoteItemIds);
        foreach ($collection->getItems() as $quoteItem) {
            $quoteItemId = $quoteItem->getId();
            $this->quoteItemWrapperFactory->create($quoteItem)->setSourceCode($sourceCodes[$quoteItemId] ?? null);
        }
    }
}