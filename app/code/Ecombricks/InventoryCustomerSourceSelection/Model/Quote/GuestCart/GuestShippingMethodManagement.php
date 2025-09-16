<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Quote\GuestCart;

/**
 * Guest shipping method management
 */
class GuestShippingMethodManagement implements \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\GuestCart\GuestShippingMethodManagementInterface
{
    /**
     * Shipping method management
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\ShippingMethodManagementInterface
     */
    private $shippingMethodManagement;

    /**
     * Quote ID mask factory
     * 
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * Constructor
     *
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\ShippingMethodManagementInterface $shippingMethodManagement
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\ShippingMethodManagementInterface $shippingMethodManagement,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
    )
    {
        $this->shippingMethodManagement = $shippingMethodManagement;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
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
        return $this->shippingMethodManagement->getMultiple(
            (int) $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id')->getQuoteId()
        );
    }
}