<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Quote\Quote\Address\Total;

/**
 * Shipping total plugin
 */
class Shipping extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Quote address wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory
     */
    private $quoteAddressWrapperFactory;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory
    )
    {
        parent::__construct($wrapperFactory);
        $this->quoteAddressWrapperFactory = $quoteAddressWrapperFactory;
    }

    /**
     * Around collect
     * 
     * @param \Magento\Quote\Model\Quote\Address\Total\Shipping $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return \Magento\Quote\Model\Quote\Address\Total\Shipping
     */
    public function aroundCollect(
        \Magento\Quote\Model\Quote\Address\Total\Shipping $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
        $this->setSubject($subject);
        $this->invokeSubjectParentMethod(\Magento\Quote\Model\Quote\Address\Total\AbstractTotal::class, 'collect', $quote, $shippingAssignment, $total);
        $shipping = $shippingAssignment->getShipping();
        $address = $shipping->getAddress();
        $code = $subject->getCode();
        $total->setTotalAmount($code, 0);
        $total->setBaseTotalAmount($code, 0);
        $items = $shippingAssignment->getItems();
        if (!count($items)) {
            return $subject;
        }
        $data = $this->getAssignmentWeightData($address, $items);
        $address->setItemQty($data['addressQty']);
        $address->setWeight($data['addressWeight']);
        $address->setFreeMethodWeight($data['freeMethodWeight']);
        $addressFreeShipping = (bool) $address->getFreeShipping();
        $freeShipping = $this->getSubjectPropertyValue('freeShipping')->isFreeShipping($quote, $items);
        $address->setFreeShipping($freeShipping);
        if (!$addressFreeShipping && $freeShipping) {
            $data = $this->getAssignmentWeightData($address, $items);
            $address->setItemQty($data['addressQty']);
            $address->setWeight($data['addressWeight']);
            $address->setFreeMethodWeight($data['freeMethodWeight']);
        }
        $address->collectShippingRates();
        $shippingMethods = $shipping->getMethod();
        if (empty($shippingMethods)) {
            return $subject;
        }
        $store = $quote->getStore();
        $basePrice = 0;
        $price = 0;
        $shippingRates = $address->getShippingRateByCode($shippingMethods);
        foreach ($shippingRates as $shippingRate) {
            $shippingRatePrice = $shippingRate->getPrice();
            $basePrice += $shippingRatePrice;
            $price += $this->getSubjectPropertyValue('priceCurrency')->convert($shippingRatePrice, $store);
        }
        $total->setBaseTotalAmount($code, $basePrice);
        $total->setTotalAmount($code, $price);
        $total->setBaseShippingAmount($basePrice);
        $total->setShippingAmount($price);
        $this->quoteAddressWrapperFactory->create($address)->generateShippingDescription($shippingMethods);
        $total->setShippingDescription($address->getShippingDescription());
        return $subject;
    }

    /**
     * Get item row weight
     *
     * @param bool $addressFreeShipping
     * @param float $itemWeight
     * @param float $itemQty
     * @param mixed $freeShipping
     * @return float
     */
    private function getItemRowWeight(bool $addressFreeShipping, float $itemWeight, float $itemQty, $freeShipping): float
    {
        $rowWeight = $itemWeight * $itemQty;
        if ($addressFreeShipping || $freeShipping === true) {
            $rowWeight = 0;
        } elseif (is_numeric($freeShipping)) {
            $freeQty = $freeShipping;
            if ($itemQty > $freeQty) {
                $rowWeight = $itemWeight * ($itemQty - $freeQty);
            } else {
                $rowWeight = 0;
            }
        }
        return (float) $rowWeight;
    }
    
    /**
     * Get item weight data
     * 
     * @param \Magento\Quote\Api\Data\CartItemInterface $item
     * @param bool $addressFreeShipping
     * @return array
     */
    private function getItemWeightData(\Magento\Quote\Api\Data\CartItemInterface $item, bool $addressFreeShipping): array
    {
        $itemWeight = (float) $item->getWeight();
        $itemQty = (float) $item->getTotalQty();
        $rowWeight = $this->getItemRowWeight($addressFreeShipping, $itemWeight, $itemQty, $item->getFreeShipping());
        $item->setRowWeight($rowWeight);
        return [
            'weight' => $itemWeight * $itemQty,
            'rowWeight' => $rowWeight,
        ];
    }
    
    /**
     * Get assignment weight data
     * 
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @param array $items
     * @return array
     */
    private function getAssignmentWeightData(\Magento\Quote\Api\Data\AddressInterface $address, array $items): array
    {
        $address->setWeight(0);
        $address->setFreeMethodWeight(0);
        $addressWeight = $address->getWeight();
        $freeMethodWeight = $address->getFreeMethodWeight();
        $addressFreeShipping = (bool) $address->getFreeShipping();
        $addressQty = 0;
        foreach ($items as $item) {
            $product = $item->getProduct();
            $isVirtual = $product->isVirtual();
            if ($isVirtual || $item->getParentItem()) {
                continue;
            }
            $itemQty = (float) $item->getQty();
            if ($item->getHasChildren() && $item->isShipSeparately()) {
                $weightType = $product->getWeightType();
                foreach ($item->getChildren() as $childItem) {
                    if ($childItem->getProduct()->isVirtual()) {
                        continue;
                    }
                    $addressQty += $childItem->getTotalQty();
                    if ($weightType) {
                        continue;
                    }
                    $itemWeightData = $this->getItemWeightData($childItem, $addressFreeShipping);
                    $addressWeight += $itemWeightData['weight'];
                    $freeMethodWeight += $itemWeightData['rowWeight'];
                }
                if (!$weightType) {
                    continue;
                }
                $itemWeightData = $this->getItemWeightData($item, $addressFreeShipping);
                $addressWeight += $itemWeightData['weight'];
                $freeMethodWeight += $itemWeightData['rowWeight'];
            } else {
                $addressQty += $itemQty;
                $itemWeightData = $this->getItemWeightData($item, $addressFreeShipping);
                $addressWeight += $itemWeightData['weight'];
                $freeMethodWeight += $itemWeightData['rowWeight'];
            }
        }
        return [
            'addressQty' => $addressQty,
            'addressWeight' => $addressWeight,
            'freeMethodWeight' => $freeMethodWeight,
        ];
    }
}