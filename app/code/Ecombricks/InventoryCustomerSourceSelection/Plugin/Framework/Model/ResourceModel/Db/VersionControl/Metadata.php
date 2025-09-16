<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Framework\Model\ResourceModel\Db\VersionControl;

/**
 * Resource metadata plugin
 */
class Metadata
{
    /**
     * After get fields
     * 
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\Metadata $subject
     * @param array $result
     * @param \Magento\Framework\DataObject $entity
     * @return array
     */
    public function afterGetFields(
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Metadata $subject,
        $result,
        \Magento\Framework\DataObject $entity
    )
    {
        if (!($entity instanceof \Magento\Quote\Api\Data\CartItemInterface)) {
            return $result;
        }
        if (!array_key_exists('source_code', $result)) {
            $result['source_code'] = null;
        }
        return $result;
    }
}