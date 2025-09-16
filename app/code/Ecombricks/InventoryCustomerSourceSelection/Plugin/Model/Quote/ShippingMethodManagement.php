<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Quote;

/**
 * Shipping method management plugin
 */
class ShippingMethodManagement
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
     * Customer address repository
     *
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $customerAddressRepository;

    /**
     * Customer session
     * 
     * @var \Magento\Customer\Model\Session 
     */
    private $customerSession;
    
    /**
     * Data processor
     * 
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    private $dataProcessor;

    /**
     * Quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * Shipping method converter
     *
     * @var \Magento\Quote\Model\Cart\ShippingMethodConverter
     */
    private $shippingMethodConverter;
    
    /**
     * Quote totals collector
     * 
     * @var \Magento\Quote\Model\Quote\TotalsCollector
     */
    private $quoteTotalsCollector;
    
    /**
     * Quote address resource
     * 
     * @var \Magento\Quote\Model\ResourceModel\Quote\Address
     */
    private $quoteAddressResource;

    /**
     * Constructor
     *
     * @param \Ecombricks\InventoryCustomerSourceSelection\Data\JointData $jointData
     * @param \Ecombricks\InventoryCustomerSourceSelection\Data\Quote\ShippingMethodFullCode $shippingMethodFullCode
     * @param \Magento\Customer\Api\AddressRepositoryInterface $customerAddressRepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataProcessor
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Model\Cart\ShippingMethodConverter $shippingMethodConverter
     * @param \Magento\Quote\Model\Quote\TotalsCollector $quoteTotalsCollector
     * @param \Magento\Quote\Model\ResourceModel\Quote\Address $quoteAddressResource
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Data\JointData $jointData,
        \Ecombricks\InventoryCustomerSourceSelection\Data\Quote\ShippingMethodFullCode $shippingMethodFullCode,
        \Magento\Customer\Api\AddressRepositoryInterface $customerAddressRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Reflection\DataObjectProcessor $dataProcessor,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Model\Cart\ShippingMethodConverter $shippingMethodConverter,
        \Magento\Quote\Model\Quote\TotalsCollector $quoteTotalsCollector,
        \Magento\Quote\Model\ResourceModel\Quote\Address $quoteAddressResource
    )
    {
        $this->jointData = $jointData;
        $this->shippingMethodFullCode = $shippingMethodFullCode;
        $this->customerAddressRepository = $customerAddressRepository;
        $this->customerSession = $customerSession;
        $this->dataProcessor = $dataProcessor;
        $this->quoteRepository = $quoteRepository;
        $this->shippingMethodConverter = $shippingMethodConverter;
        $this->quoteTotalsCollector = $quoteTotalsCollector;
        $this->quoteAddressResource = $quoteAddressResource;
    }
    
    /**
     * Around get
     * 
     * @param \Magento\Quote\Model\ShippingMethodManagement $subject
     * @param \Closure $proceed
     * @param int $cartId
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function aroundGet(
        \Magento\Quote\Model\ShippingMethodManagement $subject,
        \Closure $proceed,
        $cartId
    )
    {
        return null;
    }
    
    /**
     * Around get list
     * 
     * @param \Magento\Quote\Model\ShippingMethodManagement $subject
     * @param \Closure $proceed
     * @param integer $cartId
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function aroundGetList(
        \Magento\Quote\Model\ShippingMethodManagement $subject,
        \Closure $proceed,
        $cartId
    )
    {
        $quote = $this->quoteRepository->getActive($cartId);
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }
        $shippingAddress = $quote->getShippingAddress();
        if (!$shippingAddress->getCountryId()) {
            throw new \Magento\Framework\Exception\StateException(__('The shipping address is missing. Set the address and try again.'));
        }
        $shippingAddress->collectShippingRates();
        $shippingRates = $shippingAddress->getGroupedAllShippingRates();
        if (empty($shippingRates)) {
            return [];
        }
        $output = [];
        $quoteCurrencyCode = $quote->getQuoteCurrencyCode();
        foreach ($shippingRates as $sourceShippingRates) {
            foreach ($sourceShippingRates as $carrierShippingRates) {
                foreach ($carrierShippingRates as $shippingRate) {
                    $object = $this->shippingMethodConverter->modelToDataObject($shippingRate, $quoteCurrencyCode);
                    $output[] = $object;
                }
            }
        }
        return $output;
    }

    /**
     * Around apply
     * 
     * @param \Magento\Quote\Model\ShippingMethodManagement $subject
     * @param \Closure $proceed
     * @param int $cartId
     * @param string $carrierCode
     * @param string $methodCode
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Exception
     */
    public function aroundApply(
        \Magento\Quote\Model\ShippingMethodManagement $subject,
        \Closure $proceed,
        $cartId,
        $carrierCode,
        $methodCode
    )
    {
        $quote = $this->quoteRepository->getActive($cartId);
        if (0 == $quote->getItemsCount()) {
            throw new \Magento\Framework\Exception\InputException(
                __('The shipping method can\'t be set for an empty cart. Add an item to cart and try again.')
            );
        }
        if ($quote->isVirtual()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('The Cart includes virtual product(s) only, so a shipping address is not used.')
            );
        }
        $shippingAddress = $quote->getShippingAddress();
        if (!$shippingAddress->getCountryId()) {
            $this->quoteAddressResource->delete($shippingAddress);
            throw new \Magento\Framework\Exception\StateException(__('The shipping address is missing. Set the address and try again.'));
        }
        $shippingMethods = [];
        $shippingCarrierCodes = $this->jointData->parse($carrierCode);
        $shippingMethodCodes = $this->jointData->parse($methodCode);
        foreach ($shippingCarrierCodes as $sourceCode => $shippingCarrierCode) {
            if (empty($shippingMethodCodes[$sourceCode])) {
                continue;
            }
            $shippingMethods[$sourceCode] = $this->shippingMethodFullCode->generate($shippingCarrierCode, $shippingMethodCodes[$sourceCode]);
        }
        $shippingAddress->setShippingMethod($shippingMethods);
    }

    /**
     * Around estimate by address
     * 
     * @param \Magento\Quote\Model\ShippingMethodManagement $subject
     * @param \Closure $proceed
     * @param mixed $cartId
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[]
     */
    public function aroundEstimateByAddress(
        \Magento\Quote\Model\ShippingMethodManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Quote\Api\Data\EstimateAddressInterface $address
    )
    {
        $quote = $this->quoteRepository->getActive($cartId);
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }
        return $this->getShippingMethods($quote, $address);
    }

    /**
     * Around estimate by extended address
     * 
     * @param \Magento\Quote\Model\ShippingMethodManagement $subject
     * @param \Closure $proceed
     * @param mixed $cartId
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[]
     */
    public function aroundEstimateByExtendedAddress(
        \Magento\Quote\Model\ShippingMethodManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Quote\Api\Data\AddressInterface $address
    )
    {
        $quote = $this->quoteRepository->getActive($cartId);
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }
        return $this->getShippingMethods($quote, $address);
    }

    /**
     * Around estimate by address ID
     * 
     * @param \Magento\Quote\Model\ShippingMethodManagement $subject
     * @param \Closure $proceed
     * @param mixed $cartId
     * @param integer $addressId
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[]
     */
    public function aroundEstimateByAddressId(
        \Magento\Quote\Model\ShippingMethodManagement $subject,
        \Closure $proceed,
        $cartId,
        $addressId
    )
    {
        $quote = $this->quoteRepository->getActive($cartId);
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }
        $address = $this->customerAddressRepository->getById($addressId);
        return $this->getShippingMethods($quote, $address);
    }

    /**
     * Get shipping methods
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Framework\Api\ExtensibleDataInterface $address
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[]
     */
    private function getShippingMethods(\Magento\Quote\Model\Quote $quote, $address)
    {
        $output = [];
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->addData($this->extractAddressData($address));
        $shippingAddress->setCollectShippingRates(true);
        $this->quoteTotalsCollector->collectAddressTotals($quote, $shippingAddress);
        $quoteCustomerGroupId = $quote->getCustomerGroupId();
        $customerGroupId = $this->customerSession->getCustomerGroupId();
        $isCustomerGroupChanged = $quoteCustomerGroupId !== $customerGroupId;
        if ($isCustomerGroupChanged) {
            $quote->setCustomerGroupId($customerGroupId);
        }
        $shippingRates = $shippingAddress->getGroupedAllShippingRates();
        $quoteCurrencyCode = $quote->getQuoteCurrencyCode();
        foreach ($shippingRates as $sourceShippingRates) {
            foreach ($sourceShippingRates as $carrierShippingRates) {
                foreach ($carrierShippingRates as $shippingRate) {
                    $object = $this->shippingMethodConverter->modelToDataObject($shippingRate, $quoteCurrencyCode);
                    $output[] = $object;
                }
            }
        }
        if ($isCustomerGroupChanged) {
            $quote->setCustomerGroupId($quoteCustomerGroupId);
        }
        return $output;
    }

    /**
     * Extract address data
     * 
     * @param \Magento\Framework\Api\ExtensibleDataInterface $address
     * @return array
     */
    private function extractAddressData($address)
    {
        $className = \Magento\Customer\Api\Data\AddressInterface::class;
        if ($address instanceof \Magento\Quote\Api\Data\AddressInterface) {
            $className = \Magento\Quote\Api\Data\AddressInterface::class;
        } elseif ($address instanceof \Magento\Quote\Api\Data\EstimateAddressInterface) {
            $className = \Magento\Quote\Api\Data\EstimateAddressInterface::class;
        }
        return $this->dataProcessor->buildOutputDataArray($address, $className);
    }
}