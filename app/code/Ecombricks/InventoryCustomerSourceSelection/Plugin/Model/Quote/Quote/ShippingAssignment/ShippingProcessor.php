<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Quote\Quote\ShippingAssignment;

/**
 * Quote shipping assignment shipping processor plugin
 */
class ShippingProcessor
{
    /**
     * Joint data
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Data\JointData
     */
    private $jointData;

    /**
     * Shipping method full code
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Data\Quote\ShippingMethodFullCode
     */
    private $shippingMethodFullCode;

    /**
     * Shipping method management
     * 
     * @var \Magento\Quote\Model\ShippingMethodManagement
     */
    private $shippingMethodManagement;

    /**
     * Shipping address management
     * 
     * @var \Magento\Quote\Model\ShippingAddressManagement
     */
    private $shippingAddressManagement;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Data\JointData $jointData
     * @param \Ecombricks\InventoryCustomerSourceSelection\Data\Quote\ShippingMethodFullCode $shippingMethodFullCode
     * @param \Magento\Quote\Model\ShippingMethodManagement $shippingMethodManagement
     * @param \Magento\Quote\Model\ShippingAddressManagement $shippingAddressManagement
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Data\JointData $jointData,
        \Ecombricks\InventoryCustomerSourceSelection\Data\Quote\ShippingMethodFullCode $shippingMethodFullCode,
        \Magento\Quote\Model\ShippingMethodManagement $shippingMethodManagement,
        \Magento\Quote\Model\ShippingAddressManagement $shippingAddressManagement
    )
    {
        $this->jointData = $jointData;
        $this->shippingMethodFullCode = $shippingMethodFullCode;
        $this->shippingMethodManagement = $shippingMethodManagement;
        $this->shippingAddressManagement = $shippingAddressManagement;
    }
    
    /**
     * Around save
     * 
     * @param \Magento\Quote\Model\Quote\ShippingAssignment\ShippingProcessor $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Api\Data\ShippingInterface $shipping
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return void
     */
    public function aroundSave(
        \Magento\Quote\Model\Quote\ShippingAssignment\ShippingProcessor $subject,
        \Closure $proceed,
        \Magento\Quote\Api\Data\ShippingInterface $shipping,
        \Magento\Quote\Api\Data\CartInterface $quote
    )
    {
        $shippingAddress = $shipping->getAddress();
        $this->shippingAddressManagement->assign($quote->getId(), $shippingAddress);
        $shippingMethods = $shipping->getMethod();
        if (empty($shippingMethods) || $quote->getItemsCount() <= 0) {
            return $this;
        }
        $shippingCarrierCodes = [];
        $shippingMethodCodes = [];
        foreach ($shippingMethods as $sourceCode => $shippingMethod) {
            list($shippingCarrierCode, $shippingMethodCode) = $this->shippingMethodFullCode->parse($shippingMethod);
            $shippingCarrierCodes[$sourceCode] = $shippingCarrierCode;
            $shippingMethodCodes[$sourceCode] = $shippingMethodCode;
        }
        $this->shippingMethodManagement->apply(
            $quote->getId(), 
            $this->jointData->generate($shippingCarrierCodes), 
            $this->jointData->generate($shippingMethodCodes)
        );
    }
}