<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Tax\Sales\Total\Quote;

/**
 * Tax shipping total plugin
 */
class Shipping extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Around collect
     * 
     * @param \Magento\Tax\Model\Sales\Total\Quote\Shipping $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return \Magento\Tax\Model\Sales\Total\Quote\Shipping
     */
    public function aroundCollect(
        \Magento\Tax\Model\Sales\Total\Quote\Shipping $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
        $this->setSubject($subject);
        $items = $shippingAssignment->getItems();
        if (!$items) {
            return $subject;
        }
        $shippingTaxDetailsItem = $this->getShippingTaxDetailsItem($quote, $shippingAssignment, $total, false);
        $baseShippingTaxDetailsItem = $this->getShippingTaxDetailsItem($quote, $shippingAssignment, $total, true);
        if ($shippingTaxDetailsItem === null || $baseShippingTaxDetailsItem === null) {
            return $subject;
        }
        $shipping = $shippingAssignment->getShipping();
        $address = $shipping->getAddress();
        $address->setShippingAmount($shippingTaxDetailsItem->getRowTotal());
        $address->setBaseShippingAmount($baseShippingTaxDetailsItem->getRowTotal());
        $this->invokeSubjectMethod('processShippingTaxInfo', $shippingAssignment, $total, $shippingTaxDetailsItem, $baseShippingTaxDetailsItem);
        return $subject;
    }

    /**
     * Get shipping tax details item
     * 
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @param bool $useBaseCurrency
     * @return \Magento\Tax\Api\Data\TaxDetailsItemInterface|null
     */
    private function getShippingTaxDetailsItem(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total,
        bool $useBaseCurrency
    ): ?\Magento\Tax\Api\Data\TaxDetailsItemInterface
    {
        $subject = $this->getSubject();
        $shippingDataObject = $subject->getShippingDataObject($shippingAssignment, $total, $useBaseCurrency);
        if ($shippingDataObject === null) {
            return null;
        }
        $quoteDetails = $this->invokeSubjectMethod('prepareQuoteDetails', $shippingAssignment, [$shippingDataObject]);
        $taxDetails = $this->getSubjectPropertyValue('taxCalculationService')->calculateTax($quoteDetails, $quote->getStoreId());
        $taxDetailsItems = $taxDetails->getItems();
        return $taxDetailsItems[\Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector::ITEM_CODE_SHIPPING] ?? null;
    }
}