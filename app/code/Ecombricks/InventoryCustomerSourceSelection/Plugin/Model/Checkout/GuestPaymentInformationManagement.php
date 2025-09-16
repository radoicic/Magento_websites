<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Checkout;

/**
 * Guest payment information management plugin
 */
class GuestPaymentInformationManagement
{
    /**
     * Quote address wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory
     */
    private $quoteAddressWrapperFactory;
    
    /**
     * Payment method management
     * 
     * @var \Magento\Quote\Api\PaymentMethodManagementInterface
     */
    private $paymentMethodManagement;
    
    /**
     * Quote ID mask factory
     * 
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;
    
    /**
     * Cart repository
     * 
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $cartRepository;
    
    /**
     * Payment rate limiter
     * 
     * @var \Magento\Checkout\Api\PaymentProcessingRateLimiterInterface
     */
    private $paymentRateLimiter;
    
    /**
     * Product metadata
     * 
     * @var \Magento\Framework\App\ProductMetadata
     */
    private $productMetadata;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Magento\Framework\App\ProductMetadata $productMetadata
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Framework\App\ProductMetadata $productMetadata
    )
    {
        $this->quoteAddressWrapperFactory = $quoteAddressWrapperFactory;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->cartRepository = $cartRepository;
        $this->productMetadata = $productMetadata;
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
     * @param \Magento\Checkout\Model\GuestPaymentInformationManagement $subject
     * @param \Closure $proceed
     * @param integer $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function aroundSavePaymentInformation(
        \Magento\Checkout\Model\GuestPaymentInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        $email,
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
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        $quote = $this->cartRepository->getActive($quoteIdMask->getQuoteId());
        if ($billingAddress) {
            $billingAddress->setEmail($email);
            $quote->removeAddress($quote->getBillingAddress()->getId());
            $quote->setBillingAddress($billingAddress);
            $quote->setDataChanges(true);
        } else {
            $quote->getBillingAddress()->setEmail($email);
        }
        $shippingAddress = $quote->getShippingAddress();
		/*
        $shippingAddressWrapper = $this->quoteAddressWrapperFactory->create($shippingAddress);
        $shippingMethods = ($shippingAddress) ? $shippingAddress->getShippingMethod() : [];
        if (!empty($shippingMethods)) {
            $limitCarriers = [];
            foreach ($shippingMethods as $sourceCode => $shippingMethod) {
                $sourceShippingRate = $shippingAddressWrapper->getShippingRateByCode((string) $sourceCode, $shippingMethod);
                $limitCarriers[(string) $sourceCode] = $sourceShippingRate->getCarrier();
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
		
        $this->paymentMethodManagement->set($quoteIdMask->getQuoteId(), $paymentMethod);
        return true;
    }
}