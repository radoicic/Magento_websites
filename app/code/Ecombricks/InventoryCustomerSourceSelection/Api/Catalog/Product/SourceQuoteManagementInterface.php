<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Api\Catalog\Product;

/**
 * Product source quote management interface
 */
interface SourceQuoteManagementInterface
{
    /**
     * Get list
     * 
     * @param string $sku
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @param mixed $request
     * @return \Ecombricks\InventoryCustomerSourceSelection\Api\Catalog\Data\Product\SourceQuoteInterface[]
     */
    public function getList($sku, $address, $request = []);
}