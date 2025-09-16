<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryNzboxer\Plugin\Model\Firecheckout;

class ShippingAddressManagement extends \Ecombricks\Common\Plugin\Plugin
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
     * @var \Magento\Checkout\Api\ShippingInformationManagementInterface
     */
    private $shippingInformationManagement;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Magento\Quote\Api\PaymentMethodManagementInterface
     */
    private $paymentMethodManagement;

    /**
     * @var \Magento\Checkout\Model\PaymentDetailsFactory
     */
    private $paymentDetailsFactory;

    /**
     * @var \Magento\Quote\Api\CartTotalRepositoryInterface
     */
    private $cartTotalsRepository;

    /**
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Data\JointData $jointData
     * @param \Ecombricks\InventoryCustomerSourceSelection\Data\Quote\ShippingMethodFullCode $shippingMethodFullCode
     * @param \Magento\Checkout\Api\ShippingInformationManagementInterface $shippingInformationManagement
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
     * @param \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory
     * @param \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Data\JointData $jointData,
        \Ecombricks\InventoryCustomerSourceSelection\Data\Quote\ShippingMethodFullCode $shippingMethodFullCode,
        \Magento\Checkout\Api\ShippingInformationManagementInterface $shippingInformationManagement,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository
    ) {
        parent::__construct($wrapperFactory);
        $this->jointData = $jointData;
        $this->shippingMethodFullCode = $shippingMethodFullCode;
        $this->shippingInformationManagement = $shippingInformationManagement;
        $this->quoteRepository = $quoteRepository;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->paymentDetailsFactory = $paymentDetailsFactory;
        $this->cartTotalsRepository = $cartTotalsRepository;
    }
    
    /**
     * @param \Swissup\Firecheckout\Model\ShippingAddressManagement $subject
     * @param \Closure $proceed
     * @param integer $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    public function aroundSaveShippingAddress(
        \Swissup\Firecheckout\Model\ShippingAddressManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    )
    {
        $this->setSubject($subject);
        try {
            $street = $addressInformation->getShippingAddress()->getData('street');
            if (is_array($street)) {
                $addressInformation->getShippingAddress()->setData(
                    'street',
                    trim(implode("\n", $street))
                );
            }
            return $this->shippingInformationManagement->saveAddressInformation($cartId, $addressInformation);
        } catch (\Magento\Framework\Exception\StateException $e) {
            $quote = $this->quoteRepository->getActive($cartId);
            $address = $addressInformation->getShippingAddress();
            if ($address) {
                $address->setLimitCarrier($this->getShippingCarrierCodes($addressInformation));
            }
            $reflectedClass = new \ReflectionClass($this->shippingInformationManagement);
            $method = $reflectedClass->getMethod('prepareShippingAssignment');
            $method->setAccessible(true);
            $method->invoke($this->shippingInformationManagement, $quote, $address, $this->getShippingMethods($addressInformation));
            try {
                $this->quoteRepository->save($quote);
            } catch (\Exception $e) {}
        } catch (\Exception $e) {}
        $paymentDetails = $this->paymentDetailsFactory->create();
        $paymentDetails->setPaymentMethods($this->paymentMethodManagement->getList($cartId));
        $paymentDetails->setTotals($this->cartTotalsRepository->get($cartId));
        return $paymentDetails;
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