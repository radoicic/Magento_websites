<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Api\Catalog\Data\Product;

/**
 * Product source quote interface
 */
interface SourceQuoteInterface
{
    /**
     * Source code
     */
    const KEY_SOURCE_CODE = 'source_code';

    /**
     * Shipping method
     */
    const KEY_SHIPPING_METHOD = 'shipping_method';

    /**
     * Shipping rates
     */
    const KEY_SHIPPING_RATES = 'shipping_rates';

    /**
     * Totals
     */
    const KEY_TOTALS = 'totals';

    /**
     * Get source code
     *
     * @return string
     */
    public function getSourceCode();

    /**
     * Set source code
     *
     * @param string $sourceCode
     * @return $this
     */
    public function setSourceCode($sourceCode);

    /**
     * Get shipping method
     * 
     * @return string
     */
    public function getShippingMethod();

    /**
     * Set shipping method
     *
     * @param string $shippingMethod
     * @return $this
     */
    public function setShippingMethod($shippingMethod);

    /**
     * Get shipping rates
     *
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[]
     */
    public function getShippingRates();

    /**
     * Set shipping rates
     *
     * @param \Magento\Quote\Api\Data\ShippingMethodInterface[] $shippingRates
     * @return $this
     */
    public function setShippingRates($shippingRates);

    /**
     * Get totals
     *
     * @return \Magento\Quote\Api\Data\TotalsInterface
     */
    public function getTotals();

    /**
     * Set totals
     *
     * @param \Magento\Quote\Api\Data\TotalsInterface $totals
     * @return $this
     */
    public function setTotals($totals);
}