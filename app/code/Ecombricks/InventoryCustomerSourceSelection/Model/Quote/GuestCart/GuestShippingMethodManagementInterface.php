<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Quote\GuestCart;

/**
 * Guest shipping method management interface
 */
interface GuestShippingMethodManagementInterface
{
    /**
     * Get
     * 
     * @param integer $cartId
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function getMultiple($cartId);
}