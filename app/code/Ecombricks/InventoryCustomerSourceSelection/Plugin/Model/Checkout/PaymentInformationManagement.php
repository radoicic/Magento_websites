<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Checkout;

/**
 * Payment information management plugin
 */
class PaymentInformationManagement
{
    /**
     * Quote address wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory
     */
    private $quoteAddressWrapperFactory;

    /**
     * Product metadata
     * 
     * @var \Magento\Framework\App\ProductMetadata 
     */
    private $productMetadata;
    
    /**
     * Cart repository
     * 
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $cartRepository;
    
    /**
     * Payment method management
     * 
     * @var \Magento\Quote\Api\PaymentMethodManagementInterface
     */
    private $paymentMethodManagement;

    /**
     * Payment rate limiter
     * 
     * @var \Magento\Checkout\Api\PaymentProcessingRateLimiterInterface
     */
    private $paymentRateLimiter;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory
     * @param \Magento\Framework\App\ProductMetadata $productMetadata
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory,
        \Magento\Framework\App\ProductMetadata $productMetadata,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
    )
    {
        $this->quoteAddressWrapperFactory = $quoteAddressWrapperFactory;
        $this->productMetadata = $productMetadata;
        $this->cartRepository = $cartRepository;
        $this->paymentMethodManagement = $paymentMethodManagement;
        if (
            version_compare($this->productMetadata->getVersion(), '2.4.1', '>=') || 
            (
                version_compare($this->productMetadata->getVersion(), '2.3.6', '>=') && 
                version_compare($this->productMetadata->getVersion(), '2.4.0', '<')
            )
        ) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->paymentRateLimiter = $objectManager->get(\Magento\Checkout\Api\PaymentProcessingRateLimiterInterface::class);
        }
    }

    /**
     * Around save payment information
     * 
     * @param \Magento\Checkout\Model\PaymentInformationManagement $subject
     * @param \Closure $proceed
     * @param integer $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function aroundSavePaymentInformation(
        \Magento\Checkout\Model\PaymentInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    )
    {
        if (
            version_compare($this->productMetadata->getVersion(), '2.4.1', '>=') || 
            (
                version_compare($this->productMetadata->getVersion(), '2.3.6', '>=') && 
                version_compare($this->productMetadata->getVersion(), '2.4.0', '<')
            )
        ) {
            $this->paymentRateLimiter->limit();
        }
        if (empty($billingAddress)) {
            $this->paymentMethodManagement->set($cartId, $paymentMethod);
            return true;
        }
        $quote = $this->cartRepository->getActive($cartId);
        if (version_compare($this->productMetadata->getVersion(), '2.3.2', '>=')) {
            $customerId = $quote->getBillingAddress()->getCustomerId();
            if (!$billingAddress->getCustomerId() && $customerId) {
                $billingAddress->setCustomerId($customerId);
            }
        }
        $quote->removeAddress($quote->getBillingAddress()->getId());
        $quote->setBillingAddress($billingAddress);
        $quote->setDataChanges(true);
        $shippingAddress = $quote->getShippingAddress();
		/*
        $shippingAddressWrapper = $this->quoteAddressWrapperFactory->create($shippingAddress);
        $shippingMethods = ($shippingAddress) ? $shippingAddress->getShippingMethod() : [];
        if (!empty($shippingMethods)) {
            $limitCarriers = [];
            foreach ($shippingMethods as $sourceCode => $shippingMethod) {
                $sourceShippingRate = $shippingAddressWrapper->getShippingRateByCode((string) $sourceCode, $shippingMethod);
                $limitCarriers[$sourceCode] = $sourceShippingRate ? $sourceShippingRate->getCarrier() : $shippingMethod;
            }
            $shippingAddress->setLimitCarrier($limitCarriers);
        }
		*/
		
		    if ($shippingAddress && $shippingAddress->getShippingMethod()) {
                $shippingRate = $shippingAddress->getShippingRateByCode($shippingAddress->getShippingMethod());
                if ($shippingRate) {
                    $shippingAddress->setLimitCarrier($shippingRate->getCarrier());
                }
            }
		
		
        $this->paymentMethodManagement->set($cartId, $paymentMethod);
        return true;
    }
}