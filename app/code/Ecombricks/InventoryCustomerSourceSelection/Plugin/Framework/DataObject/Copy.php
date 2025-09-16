<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Framework\DataObject;

/**
 * Object copy plugin
 */
class Copy
{
    /**
     * After get data from field set
     * 
     * @param \Magento\Framework\DataObject\Copy $subject
     * @param array $result
     * @param string $fieldset
     * @return array
     */
    public function afterGetDataFromFieldset(
        \Magento\Framework\DataObject\Copy $subject,
        $result,
        $fieldset
    )
    {
        if ($fieldset === 'sales_convert_quote_address' && array_key_exists('shipping_method', $result)) {
            unset($result['shipping_method']);
        }
        return $result;
    }
}