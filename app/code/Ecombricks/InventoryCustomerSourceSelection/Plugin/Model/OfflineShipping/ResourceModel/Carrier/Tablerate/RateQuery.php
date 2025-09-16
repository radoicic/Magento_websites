<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\OfflineShipping\ResourceModel\Carrier\Tablerate;

/**
 * Shipping table rate rate query plugin
 */
class RateQuery
{
    /**
     * After prepare select
     * 
     * @param \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\RateQuery $subject
     * @param \Magento\Framework\DB\Select $result
     * @return \Magento\Framework\DB\Select
     */
    public function afterPrepareSelect(
        \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\RateQuery $subject,
        $result
    )
    {
        $result->order('source_code DESC');
        $result->where('(source_code = :source_code) OR (source_code IS NULL)');
        return $result;
    }

    /**
     * After get bindings
     * 
     * @param \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\RateQuery $subject
     * @param array $result
     * @return array
     */
    public function afterGetBindings(
        \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\RateQuery $subject,
        $result
    )
    {
        $result[':source_code'] = $subject->getRequest()->getSourceCode();
        return $result;
    }
}