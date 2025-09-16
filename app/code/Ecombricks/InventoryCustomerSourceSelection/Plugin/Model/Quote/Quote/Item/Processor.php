<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Quote\Quote\Item;

/**
 * Quote item processor model plugin
 */
class Processor extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Quote item wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory
     */
    private $quoteItemWrapperFactory;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory $quoteItemWrapperFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory $quoteItemWrapperFactory
    )
    {
        parent::__construct($wrapperFactory);
        $this->quoteItemWrapperFactory = $quoteItemWrapperFactory;
    }

    /**
     * After initialize
     * 
     * @param \Magento\Quote\Model\Quote\Item\Processor $subject
     * @param \Magento\Quote\Model\Quote\Item $result
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\DataObject $request
     * @return \Magento\Quote\Model\Quote\Item
     */
    public function afterInit(
        \Magento\Quote\Model\Quote\Item\Processor $subject,
        $result,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\DataObject $request
    )
    {
        $this->quoteItemWrapperFactory->create($result)->setSourceCode($request->getSource());
        return $result;
    }
    
    /**
     * After prepare
     * 
     * @param \Magento\Quote\Model\Quote\Item\Processor $subject
     * @param null $result
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param \Magento\Framework\DataObject $request
     * @param \Magento\Catalog\Model\Product $candidate
     * @return void
     */
    public function afterPrepare(
        \Magento\Quote\Model\Quote\Item\Processor $subject,
        $result,
        \Magento\Quote\Model\Quote\Item $item,
        \Magento\Framework\DataObject $request
    )
    {
        $this->quoteItemWrapperFactory->create($item)->setSourceCode($request->getSource());
    }
}