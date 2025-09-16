<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Quote\ResourceModel\Quote\Address;

/**
 * Quote address rate resource plugin
 */
class Rate extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Before get main table
     * 
     * @param \Magento\Quote\Model\ResourceModel\Quote\Address\Rate $subject
     * @return void
     */
    public function beforeGetMainTable(\Magento\Quote\Model\ResourceModel\Quote\Address\Rate $subject)
    {
        $this->setSubject($subject);
        $this->invokeSubjectMethod('_setMainTable', 'ecombricks_inventory__source_quote_shipping_rate', 'rate_id');
    }
}