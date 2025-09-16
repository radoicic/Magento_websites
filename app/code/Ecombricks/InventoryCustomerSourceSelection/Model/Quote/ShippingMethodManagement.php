<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Quote;

/**
 * Shipping method management
 */
class ShippingMethodManagement implements \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\ShippingMethodManagementInterface
{
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
     * Constructor
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Model\Cart\ShippingMethodConverter $shippingMethodConverter
     * @return void
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Model\Cart\ShippingMethodConverter $shippingMethodConverter
    )
    {
        $this->quoteRepository = $quoteRepository;
        $this->shippingMethodConverter = $shippingMethodConverter;
    }

    /**
     * Get multiple
     *
     * @param integer $cartId
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function getMultiple($cartId)
    {
        $quote = $this->quoteRepository->getActive($cartId);
        $shippingAddress = $quote->getShippingAddress();
        if (!$shippingAddress->getCountryId()) {
            throw new \Magento\Framework\Exception\StateException(__('The shipping address is missing. Set the address and try again.'));
        }
        $shippingMethods = $shippingAddress->getShippingMethod();
        if (empty($shippingMethods)) {
            return [];
        }
        $shippingAddress->collectShippingRates();
        $shippingRates = $shippingAddress->getShippingRateByCode($shippingMethods);
        if (empty($shippingRates)) {
            return [];
        }
        $output = [];
        $quoteCurrencyCode = $quote->getQuoteCurrencyCode();
        foreach ($shippingRates as $shippingRate) {
            if (!empty($shippingRate)) {
                $output[] = $this->shippingMethodConverter->modelToDataObject($shippingRate, $quoteCurrencyCode);
            }
        }
        return $output;
    }
}
