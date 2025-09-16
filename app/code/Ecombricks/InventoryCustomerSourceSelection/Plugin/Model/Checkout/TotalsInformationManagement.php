<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Checkout;

/**
 * Totals information management plugin
 */
class TotalsInformationManagement
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
     * Cart repository
     * 
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * Cart total repository
     * 
     * @var \Magento\Quote\Api\CartTotalRepositoryInterface
     */
    private $cartTotalRepository;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Data\JointData $jointData
     * @param \Ecombricks\InventoryCustomerSourceSelection\Data\Quote\ShippingMethodFullCode $shippingMethodFullCode
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalRepository
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Data\JointData $jointData,
        \Ecombricks\InventoryCustomerSourceSelection\Data\Quote\ShippingMethodFullCode $shippingMethodFullCode,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalRepository
    )
    {
        $this->jointData = $jointData;
        $this->shippingMethodFullCode = $shippingMethodFullCode;
        $this->cartRepository = $cartRepository;
        $this->cartTotalRepository = $cartTotalRepository;
    }

    /**
     * Around calculate
     * 
     * @param \Magento\Checkout\Model\TotalsInformationManagement $subject
     * @param \Closure $proceed
     * @param integer $cartId
     * @param \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
     * @return \Magento\Quote\Api\Data\TotalsInterface
     */
    public function aroundCalculate(
        \Magento\Checkout\Model\TotalsInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
    )
    {
        $quote = $this->cartRepository->get($cartId);
        if ($quote->getItemsCount() === 0) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Totals calculation is not applicable to empty cart')
            );
        }
        if ($quote->getIsVirtual()) {
            $quote->setBillingAddress($addressInformation->getAddress());
        } else {
            $quote->setShippingAddress($addressInformation->getAddress());
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setCollectShippingRates(true);
            $shippingAddress->setShippingMethod($this->getTotalsInformationShippingMethods($addressInformation));
        }
        $quote->collectTotals();
        return $this->cartTotalRepository->get($cartId);
    }

    /**
     * Get totals information shipping methods
     * 
     * @param \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
     * @return array
     */
    private function getTotalsInformationShippingMethods(\Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation)
    {
        $shippingMethods = [];
        $shippingCarrierCodes = $this->jointData->parse($addressInformation->getShippingCarrierCode());
        $shippingMethodCodes = $this->jointData->parse($addressInformation->getShippingMethodCode());
        foreach ($shippingCarrierCodes as $sourceCode => $shippingCarrierCode) {
            if (empty($shippingMethodCodes[$sourceCode])) {
                continue;
            }
            $shippingMethods[$sourceCode] = $this->shippingMethodFullCode->generate($shippingCarrierCode, $shippingMethodCodes[$sourceCode]);
        }
        return $shippingMethods;
    }
}