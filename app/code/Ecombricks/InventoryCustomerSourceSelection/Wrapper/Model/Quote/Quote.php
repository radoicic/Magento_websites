<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote;

/**
 * Quote item wrapper
 */
class Quote extends \Ecombricks\Common\DataObject\Wrapper
{
    /**
     * Quote address wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory
     */
    private $quoteAddressWrapperFactory;
    
    /**
     * Quote item wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory
     */
    private $quoteItemWrapperFactory;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory $quoteItemWrapperFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory $quoteItemWrapperFactory
    )
    {
        parent::__construct($objectReflectionFactory);
        $this->quoteAddressWrapperFactory = $quoteAddressWrapperFactory;
        $this->quoteItemWrapperFactory = $quoteItemWrapperFactory;
    }

    /**
     * Get source codes
     * 
     * @return array
     */
    public function getSourceCodes()
    {
        $sourceCodes = [];
        $quote = $this->getObject();
        foreach ($quote->getAllItems() as $item) {
            $sourceCode = $this->quoteItemWrapperFactory->create($item)->getSourceCode();
            if (empty($sourceCode)) {
                continue;
            }
            if (in_array($sourceCode, $sourceCodes)) {
                continue;
            }
            $sourceCodes[] = $sourceCode;
        }
        return $sourceCodes;
    }

    /**
     * Set source code
     * 
     * @param string $sourceCode
     * @return void
     */
    public function setSourceCode(string $sourceCode): void
    {
        $quote = $this->getObject();
        $quote->getExtensionAttributes()->setSourceCode($sourceCode);
        $this->filterSourceItems();
        $this->filterSourceAddresses();
        $quote->setTotalsCollectedFlag(false);
        $quote->collectTotals();
    }

    /**
     * Set source code
     * 
     * @return string|null
     */
    public function getSourceCode(): ?string
    {
        return $this->getObject()->getExtensionAttributes()->getSourceCode();
    }

    /**
     * Create source clone
     * 
     * @param string $sourceCode
     * @return \Magento\Quote\Api\Data\CartInterface
     */
    public function createSourceClone(string $sourceCode): \Magento\Quote\Api\Data\CartInterface
    {
        $quote = $this->getObject();
        $sourceQuote = clone $quote;
        $sourceQuote->setExtensionAttributes(clone $quote->getExtensionAttributes());
        $sourceQuote->setId($quote->getId());
        $this->setObject($sourceQuote);
        $this->setSourceCode($sourceCode);
        $this->setObject($quote);
        return $sourceQuote;
    }

    /**
     * Create item source clone
     * 
     * @param \Magento\Quote\Api\Data\CartItemInterface $item
     * @return \Magento\Quote\Api\Data\CartItemInterface
     */
    private function createItemSourceClone(\Magento\Quote\Api\Data\CartItemInterface $item): \Magento\Quote\Api\Data\CartItemInterface
    {
        $sourceItem = clone $item;
        $sourceItem->setExtensionAttributes(clone $item->getExtensionAttributes());
        $sourceItem->setId($item->getId());
        $sourceItem->setQuote($this->getObject());
        return $sourceItem;
    }
    
    /**
     * Filter source items
     * 
     * @return void
     */
    private function filterSourceItems(): void
    {
        $sourceCode = $this->getSourceCode();
        if (empty($sourceCode)) {
            return;
        }
        $quote = $this->getObject();
        $itemsCollection = $quote->getItemsCollection();
        $sourceItemsCollection = clone $itemsCollection;
        foreach ($itemsCollection as $itemKey => $item) {
            $sourceItemsCollection->removeItemByKey($itemKey);
        }
        foreach ($itemsCollection as $item) {
            if ($this->quoteItemWrapperFactory->create($item)->getSourceCode() != $sourceCode || $item->getParentItem()) {
                continue;
            }
            $sourceItem = $this->createItemSourceClone($item);
            foreach ($item->getChildren() as $childItem) {
                if ($this->quoteItemWrapperFactory->create($childItem)->getSourceCode() != $sourceCode) {
                    continue;
                }
                $sourceChildItem = $this->createItemSourceClone($childItem);
                $sourceChildItem->setParentItem($sourceItem);
                $sourceItemsCollection->addItem($sourceChildItem);
            }
            $sourceItemsCollection->addItem($sourceItem);
        }
        $sourceItemsCollection->setQuote($quote);
        $quote->unsetData(
            [
                'items_collection',
                'all_items_qty',
                'virtual_items_qty',
            ]
        );
        $this->setPropertyValue('shippingAddressesItems', null);
        $this->setPropertyValue('_items', $sourceItemsCollection);
    }

    /**
     * Create address source clone
     * 
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @return \Magento\Quote\Api\Data\AddressInterface
     */
    private function createAddressSourceClone(\Magento\Quote\Api\Data\AddressInterface $address): \Magento\Quote\Api\Data\AddressInterface
    {
        $sourceAddress = clone $address;
        $sourceAddress->setExtensionAttributes(clone $address->getExtensionAttributes());
        $sourceAddress->setId($address->getId());
        $sourceAddress->setQuote($this->getObject());
        return $sourceAddress;
    }
    
    /**
     * Filter source addresses
     * 
     * @return void
     */
    private function filterSourceAddresses(): void
    {
        $sourceCode = $this->getSourceCode();
        if (empty($sourceCode)) {
            return;
        }
        $quote = $this->getObject();
        $quote->getBillingAddress();
        $quote->getShippingAddress();
        $addressesCollection = $quote->getAddressesCollection();
        $sourceAddressesCollection = clone $addressesCollection;
        foreach ($addressesCollection as $addressKey => $address) {
            $sourceAddressesCollection->removeItemByKey($addressKey);
            $sourceAddress = $this->createAddressSourceClone($address);
            $this->quoteAddressWrapperFactory->create($sourceAddress)->setSourceCode($sourceCode);
            $sourceAddressesCollection->addItem($sourceAddress);
        }
        $this->setPropertyValue('_addresses', $sourceAddressesCollection);
    }
}