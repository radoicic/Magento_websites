<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product\SourceQuote;

/**
 * Product source quote totals converter
 */
class TotalsConverter
{
    /**
     * Totals factory
     * 
     * @var \Magento\Quote\Api\Data\TotalsInterfaceFactory
     */
    private $totalsFactory;

    /**
     * Data object helper
     * 
     * @var \Magento\Framework\Api\DataObjectHelper 
     */
    private $dataObjectHelper;

    /**
     * Segments converter
     * 
     * @var \Magento\Quote\Model\Cart\TotalsConverter
     */
    private $segmentsConverter;

    /**
     * Item converter
     * 
     * @var \Magento\Quote\Model\Cart\Totals\ItemConverter
     */
    private $itemConverter;

    /**
     * Constructor
     * 
     * @param \Magento\Quote\Api\Data\TotalsInterfaceFactory $totalsFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Quote\Model\Cart\TotalsConverter $segmentsConverter
     * @param \Magento\Quote\Model\Cart\Totals\ItemConverter $itemConverter
     * @return void
     */
    public function __construct(
        \Magento\Quote\Api\Data\TotalsInterfaceFactory $totalsFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Quote\Model\Cart\TotalsConverter $segmentsConverter,
        \Magento\Quote\Model\Cart\Totals\ItemConverter $itemConverter
    )
    {
        $this->totalsFactory = $totalsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->segmentsConverter = $segmentsConverter;
        $this->itemConverter = $itemConverter;
    }

    /**
     * Process
     * 
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return \Magento\Quote\Api\Data\TotalsInterface
     */
    public function process(\Magento\Quote\Api\Data\CartInterface $quote): \Magento\Quote\Api\Data\TotalsInterface
    {
        $totals = $this->totalsFactory->create();
        $address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
        $this->dataObjectHelper->populateWithArray($totals, $this->getAddressData($address), \Magento\Quote\Api\Data\TotalsInterface::class);
        $totals->setTotalSegments($this->getSegments($address));
        $totals->setGrandTotal($this->getGrandTotal($totals));
        $totals->setItems($this->getItems($quote));
        $totals->setItemsQty($quote->getItemsQty());
        $totals->setBaseCurrencyCode($quote->getBaseCurrencyCode());
        $totals->setQuoteCurrencyCode($quote->getQuoteCurrencyCode());
        return $totals;
    }

    /**
     * Get address data
     * 
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @return array
     */
    private function getAddressData(\Magento\Quote\Api\Data\AddressInterface $address): array
    {
        $data = $address->getData();
        unset($data[\Magento\Framework\Api\ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);
        return $data;
    }
    
    /**
     * Get segments
     * 
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @return \Magento\Quote\Api\Data\TotalSegmentInterface[]
     */
    private function getSegments(\Magento\Quote\Api\Data\AddressInterface $address): array
    {
        return $this->segmentsConverter->process($address->getTotals());
    }
    
    /**
     * Get grand total
     * 
     * @param \Magento\Quote\Api\Data\TotalsInterface $totals
     * @return float
     */
    private function getGrandTotal(\Magento\Quote\Api\Data\TotalsInterface $totals): float
    {
        $amount = $totals->getGrandTotal() - $totals->getTaxAmount();
        return $amount > 0 ? (float) $amount : 0;
    }
    
    /**
     * Get items
     * 
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return \Magento\Quote\Api\Data\TotalsItemInterface[]
     */
    private function getItems($quote): array
    {
        $items = [];
        foreach ($quote->getAllVisibleItems() as $index => $item) {
            $items[$index] = $this->itemConverter->modelToDataObject($item);
        }
        return $items;
    }
}