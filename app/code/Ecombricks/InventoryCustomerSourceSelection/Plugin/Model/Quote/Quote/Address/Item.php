<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Quote\Quote\Address;

/**
 * Quote address item plugin
 */
class Item
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
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory $quoteItemWrapperFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory $quoteItemWrapperFactory
    )
    {
        $this->quoteItemWrapperFactory = $quoteItemWrapperFactory;
    }
    
    /**
     * After import quote item
     * 
     * @param \Magento\Quote\Model\Quote\Address\Item $subject
     * @param \Magento\Quote\Model\Quote\Address\Item $result
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return \Magento\Quote\Model\Quote\Address\Item
     */
    public function afterImportQuoteItem(
        \Magento\Quote\Model\Quote\Address\Item $subject,
        \Closure $result,
        \Magento\Quote\Model\Quote\Item $quoteItem
    )
    {
        $sourceCode = $this->quoteItemWrapperFactory->create($quoteItem)->getSourceCode();
        $subject->setSourceCode($sourceCode);
        return $result;
    }
}