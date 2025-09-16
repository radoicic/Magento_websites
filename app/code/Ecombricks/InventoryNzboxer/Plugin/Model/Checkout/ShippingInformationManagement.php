<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryNzboxer\Plugin\Model\Checkout;

class ShippingInformationManagement extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * @var \Ecombricks\InventoryCustomerSourceSelection\Data\JointData
     */
    private $jointData;

    /**
     * @var \Ecombricks\InventoryCustomerSourceSelection\Data\Quote\ShippingMethodFullCode
     */
    private $shippingMethodFullCode;
    
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var \Swissup\DeliveryDate\Model\DeliverydateFactory
     */
    private $deliverydateFactory;

    /**
     * @var \Swissup\DeliveryDate\Helper\Data
     */
    private $dataHelper;

    /**
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Magento\Quote\Model\QuoteRepository     $quoteRepository
     * @param \Swissup\DeliveryDate\Model\DeliverydateFactory $deliverydateFactory
     * @param \Swissup\DeliveryDate\Helper\Data $dataHelper
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Data\JointData $jointData,
        \Ecombricks\InventoryCustomerSourceSelection\Data\Quote\ShippingMethodFullCode $shippingMethodFullCode,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Swissup\DeliveryDate\Model\DeliverydateFactory $deliverydateFactory,
        \Swissup\DeliveryDate\Helper\Data $dataHelper
    ) {
        parent::__construct($wrapperFactory);
        $this->jointData = $jointData;
        $this->shippingMethodFullCode = $shippingMethodFullCode;
        $this->quoteRepository = $quoteRepository;
        $this->deliverydateFactory = $deliverydateFactory;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $this->setSubject($subject);
        if (!$this->dataHelper->isEnabled()) {
            return;
        }
        $date = null;
        $time = null;
        
        $isTimeRequired = false;
        $isDateRequired = true;
        foreach ($this->getShippingMethods() as $shippingMethod) {
            if ($this->dataHelper->isTimeRequired($shippingMethod)) {
                $isTimeRequired = true;
            }
            if ($this->dataHelper->isDateRequired($shippingMethod)) {
                $isDateRequired = true;
            }
        }
        $extAttributes = $addressInformation->getExtensionAttributes();
        if ($extAttributes) {
            $time = $extAttributes->getDeliveryTime();
            if (!$time && $isTimeRequired) {
                throw new \Magento\Framework\Exception\StateException(__('Delivery Time is required'));
            }
            if ($time && !in_array($time, $this->dataHelper->getTimeOptions(true))) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Invalid Delivery Time value'));
            }
            $date = $extAttributes->getDeliveryDate();
            if (!$date && $isDateRequired) {
                throw new \Magento\Framework\Exception\StateException(__('Delivery Date is required'));
            }
            $date = $this->dataHelper->formatMySqlDateTime($date);
            if (!$this->dataHelper->isValid($date)) {
                throw new \Magento\Framework\Exception\StateException(__('Invalid Delivery Date value'));
            }
        } elseif ($isTimeRequired || $isDateRequired) {
            throw new \Magento\Framework\Exception\StateException(__('Delivery Date is required'));
        }
        $quote = $this->quoteRepository->getActive($cartId);
        $modelDeliveryDate = $this->deliverydateFactory->create()->loadByQuoteId($quote->getId());
        if ($date || $time) {
            $modelDeliveryDate->setDate($date)->setTimerange($time)->setQuoteId($quote->getId())->save();
        } elseif ($modelDeliveryDate->getId()) {
            $modelDeliveryDate->delete();
        }
    }
    
    /**
     * Get shipping carrier codes
     * 
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @return array
     */
    private function getShippingCarrierCodes(\Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation): array
    {
        return $this->jointData->parse($addressInformation->getShippingCarrierCode());
    }
    
    /**
     * Get shipping method codes
     * 
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @return array
     */
    private function getShippingMethodCodes(\Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation): array
    {
        return $this->jointData->parse($addressInformation->getShippingMethodCode());
    }
    
    /**
     * Get shipping methods
     * 
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @return array
     */
    private function getShippingMethods(\Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation): array
    {
        $shippingMethods = [];
        $shippingCarrierCodes = $this->getShippingCarrierCodes($addressInformation);
        $shippingMethodCodes = $this->getShippingMethodCodes($addressInformation);
        foreach ($shippingCarrierCodes as $sourceCode => $shippingCarrierCode) {
            if (empty($shippingMethodCodes[$sourceCode])) {
                continue;
            }
            $shippingMethods[$sourceCode] = $this->shippingMethodFullCode->generate($shippingCarrierCode, $shippingMethodCodes[$sourceCode]);
        }
        return $shippingMethods;
    }
}