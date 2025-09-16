<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Checkout;

/**
 * Shipping information management plugin
 */
class ShippingInformationManagement extends \Ecombricks\Common\Plugin\Plugin
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
     * Payment details factory
     * 
     * @var \Magento\Checkout\Model\PaymentDetailsFactory
     */
    private $paymentDetailsFactory;

    /**
     * Quote repository
     * 
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * Cart total repository
     * 
     * @var \Magento\Quote\Api\CartTotalRepositoryInterface
     */
    private $cartTotalsRepository;

    /**
     * Payment method management
     * 
     * @var \Magento\Quote\Api\PaymentMethodManagementInterface
     */
    private $paymentMethodManagement;

    /**
     * Address validator
     * 
     * @var \Magento\Quote\Model\QuoteAddressValidator 
     */
    private $addressValidator;

    /**
     * Logger
     * 
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Data\JointData $jointData
     * @param \Ecombricks\InventoryCustomerSourceSelection\Data\Quote\ShippingMethodFullCode $shippingMethodFullCode
     * @param \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
     * @param \Magento\Quote\Model\QuoteAddressValidator $addressValidator
     * @param \Psr\Log\LoggerInterface $logger
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Data\JointData $jointData,
        \Ecombricks\InventoryCustomerSourceSelection\Data\Quote\ShippingMethodFullCode $shippingMethodFullCode,
        \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Quote\Model\QuoteAddressValidator $addressValidator,
        \Psr\Log\LoggerInterface $logger
    )
    {
        parent::__construct($wrapperFactory);
        $this->jointData = $jointData;
        $this->shippingMethodFullCode = $shippingMethodFullCode;
        $this->paymentDetailsFactory = $paymentDetailsFactory;
        $this->quoteRepository = $quoteRepository;
        $this->cartTotalsRepository = $cartTotalsRepository;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->addressValidator = $addressValidator;
        $this->logger = $logger;
    }

    /**
     * Around save address information
     * 
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param \Closure $proceed
     * @param integer $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function aroundSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    )
    {
        $this->setSubject($subject);
        $quote = $this->quoteRepository->getActive($cartId);
        $this->invokeSubjectMethod('validateQuote', $quote);
        $shippingAddress = $addressInformation->getShippingAddress();
        $this->validateAddress($shippingAddress);
        if (!$shippingAddress->getCustomerAddressId()) {
            $shippingAddress->setCustomerAddressId(null);
        }
        try {
            $billingAddress = $addressInformation->getBillingAddress();
            if ($billingAddress) {
                if (!$billingAddress->getCustomerAddressId()) {
                    $billingAddress->setCustomerAddressId(null);
                }
                $this->addressValidator->validateForCart($quote, $billingAddress);
                $quote->setBillingAddress($billingAddress);
            }
            $this->addressValidator->validateForCart($quote, $shippingAddress);
            $shippingAddress->setLimitCarrier($this->getShippingCarrierCodes($addressInformation));
            $this->invokeSubjectMethod('prepareShippingAssignment', $quote, $shippingAddress, $this->getShippingMethods($addressInformation));
            $quote->setIsMultiShipping(false);
            $this->quoteRepository->save($quote);
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $this->logger->critical($exception);
            throw new \Magento\Framework\Exception\InputException(
                __('The shipping information was unable to be saved. Error: "%message"', ['message' => $e->getMessage()])
            );
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            throw new \Magento\Framework\Exception\InputException(
                __('The shipping information was unable to be saved. Verify the input data and try again.')
            );
        }
        $this->validateShippingRates($quote);
        return $this->createPaymentDetails((int) $cartId);
    }
    
    /**
     * Validate address
     *
     * @param \Magento\Quote\Api\Data\AddressInterface|null $address
     * @return void
     * @throws \Magento\Framework\Exception\StateException
     */
    private function validateAddress(?\Magento\Quote\Api\Data\AddressInterface $address): void
    {
        if (!$address || !$address->getCountryId()) {
            throw new \Magento\Framework\Exception\StateException(__('The shipping address is missing. Set the address and try again.'));
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
    
    /**
     * Validate shipping rates
     * 
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function validateShippingRates(\Magento\Quote\Api\Data\CartInterface $quote): void
    {
        $shippingAddress = $quote->getShippingAddress();
        if (!$quote->getIsVirtual() && !$shippingAddress->getShippingRateByCode($shippingAddress->getShippingMethod())) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Carriers with such methods not found: %1', $shippingAddress->getShippingMethod()));
        }
    }

    /**
     * Create payment details
     * 
     * @param int $cartId
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    private function createPaymentDetails(int $cartId): \Magento\Checkout\Api\Data\PaymentDetailsInterface
    {
        $paymentDetails = $this->paymentDetailsFactory->create();
        $paymentDetails->setPaymentMethods($this->paymentMethodManagement->getList($cartId));
        $paymentDetails->setTotals($this->cartTotalsRepository->get($cartId));
        return $paymentDetails;
    }
}