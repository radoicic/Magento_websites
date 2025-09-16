<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Block\Sales\Adminhtml\Order\Create\Items;

/**
 * Create order items grid wrapper
 */
class Grid extends \Ecombricks\Common\DataObject\Wrapper implements \Magento\Framework\View\Element\Block\ArgumentInterface
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
     * Get source code
     * 
     * @param \Magento\Quote\Api\Data\CartItemInterface $quoteitem
     * @return string|null
     */
    public function getSourceCode(\Magento\Quote\Api\Data\CartItemInterface $quoteitem): ?string
    {
        $quoteItemWrapper = $this->wrapperFactory->create(
            $quoteitem,
            \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\Item::class
        );
        return $quoteItemWrapper->getSourceCode();
    }
}