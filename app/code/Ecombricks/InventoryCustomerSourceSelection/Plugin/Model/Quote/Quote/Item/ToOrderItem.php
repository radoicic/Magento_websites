<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Quote\Quote\Item;

/**
 * Quote item to order item plugin
 */
class ToOrderItem
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
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
    )
    {
        $this->wrapperFactory = $wrapperFactory;
    }

    /**
     * After convert
     * 
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $subject
     * @param \Magento\Sales\Api\Data\OrderItemInterface $result
     * @param \Magento\Quote\Model\Quote\Item|\Magento\Quote\Model\Quote\Address\Item $item
     * @param array $data
     * @return \Magento\Sales\Api\Data\OrderItemInterface
     */
    public function afterConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        $result,
        $item
    )
    {
        if ($item instanceof \Magento\Quote\Api\Data\CartItemInterface) {
            $sourceCode = (string) $this->wrapperFactory->create($item, \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\Item::class)->getSourceCode();
        } else {
            $sourceCode = (string) $item->getSourceCode();
        }
        $this->wrapperFactory->create($result, \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\Item::class)->setSourceCode($sourceCode);
        return $result;
    }
}