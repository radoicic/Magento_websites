<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Quote\Cart;

/**
 * Shipping method converter plugin
 */
class ShippingMethodConverter
{
    /**
     * After model to data object
     * 
     * @param \Magento\Quote\Model\Cart\ShippingMethodConverter $subject
     * @param \Magento\Quote\Api\Data\ShippingMethodInterface $result
     * @param \Magento\Quote\Model\Quote\Address\Rate $rateModel
     * @param string $quoteCurrencyCode
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface
     */
    public function afterModelToDataObject(
        \Magento\Quote\Model\Cart\ShippingMethodConverter $subject,
        $result,
        $rateModel,
        $quoteCurrencyCode
    )
    {
        $result->getExtensionAttributes()->setSourceCode($rateModel->getSourceCode());
        return $result;
    }
}