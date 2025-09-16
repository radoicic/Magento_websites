<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\OfflineShipping\ResourceModel\Carrier;

/**
 * Shipping table rate resource plugin
 */
class Tablerate extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Before get main table
     * 
     * @param \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate $subject
     * @return void
     */
    public function beforeGetMainTable(\Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate $subject)
    {
        $this->setSubject($subject);
        $this->invokeSubjectMethod('_setMainTable', 'ecombricks_inventory__source_shipping_tablerate', 'pk');
    }
}