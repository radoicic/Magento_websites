<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product;

/**
 * Product source quote
 */
class SourceQuote extends \Magento\Framework\Api\AbstractSimpleObject 
    implements \Ecombricks\InventoryCustomerSourceSelection\Api\Catalog\Data\Product\SourceQuoteInterface 
{
    /**
     * Get source code
     *
     * @return string
     */
    public function getSourceCode()
    {
        return $this->_get(self::KEY_SOURCE_CODE);
    }

    /**
     * Set source code
     *
     * @param string $sourceCode
     * @return $this
     */
    public function setSourceCode($sourceCode)
    {
        return $this->setData(self::KEY_SOURCE_CODE, $sourceCode);
    }
    
    /**
     * Get shipping method
     * 
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->_get(self::KEY_SHIPPING_METHOD);
    }
    
    /**
     * Set shipping method
     *
     * @param string $shippingMethod
     * @return $this
     */
    public function setShippingMethod($shippingMethod)
    {
        return $this->setData(self::KEY_SHIPPING_METHOD, $shippingMethod);
    }
    
    /**
     * Get shipping rates
     *
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[]
     */
    public function getShippingRates()
    {
        return $this->_get(self::KEY_SHIPPING_RATES);
    }
    
    /**
     * Set shipping rates
     *
     * @param \Magento\Quote\Api\Data\ShippingMethodInterface[] $shippingRates
     * @return $this
     */
    public function setShippingRates($shippingRates)
    {
        return $this->setData(self::KEY_SHIPPING_RATES, $shippingRates);
    }
    
    /**
     * Get totals
     *
     * @return \Magento\Quote\Api\Data\TotalsInterface
     */
    public function getTotals()
    {
        return $this->_get(self::KEY_TOTALS);
    }
    
    /**
     * Set totals
     *
     * @param \Magento\Quote\Api\Data\TotalsInterface $totals
     * @return $this
     */
    public function setTotals($totals)
    {
        return $this->setData(self::KEY_TOTALS, $totals);
    }
}