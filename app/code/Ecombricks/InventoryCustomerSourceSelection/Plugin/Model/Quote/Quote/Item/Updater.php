<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Quote\Quote\Item;

/**
 * Quote item updater model plugin
 */
class Updater extends \Ecombricks\Common\Plugin\Plugin
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
     * After update
     * 
     * @param \Magento\Quote\Model\Quote\Item\Updater $subject
     * @param \Magento\Quote\Model\Quote\Item\Updater $result
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param array $info
     * @throws \Zend\Code\Exception\InvalidArgumentException
     * @return \Magento\Quote\Model\Quote\Item\Updater
     */
    public function afterUpdate(
        \Magento\Quote\Model\Quote\Item\Updater $subject,
        $result,
        \Magento\Quote\Model\Quote\Item $item,
        array $info
    )
    {
        /**
        if (empty($info['source'])) {
            throw new \InvalidArgumentException((string) __('The source is required to update quote item.'));
        }*/
        if (empty($info['action']) || !empty($info['configured'])) {
            if (!empty($info['source'])) {
                $this->quoteItemWrapperFactory->create($item)->setSourceCode($info['source']);
            }
            $product = $item->getProduct();
            $product->setIsSuperMode(true);
            $product->unsSkipCheckRequiredOption();
            $item->checkData();
        }
        return $result;
    }
}