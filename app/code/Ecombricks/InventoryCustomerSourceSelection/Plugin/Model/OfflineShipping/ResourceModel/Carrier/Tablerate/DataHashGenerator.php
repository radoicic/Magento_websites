<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\OfflineShipping\ResourceModel\Carrier\Tablerate;

/**
 * Shipping table rate data hash generator plugin
 */
class DataHashGenerator
{
    /**
     * After get hash
     * 
     * @param \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\DataHashGenerator $subject
     * @param string $result
     * @param array $data
     * @return string
     */
    public function afterGetHash(
        \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\DataHashGenerator $subject,
        $result,
        array $data
    )
    {
        return $data['source_code'].'-'.$result;
    }
}