<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Api;

/**
 * Current stock provider interface
 */
interface CurrentStockProviderInterface
{
    /**
     * Get ID
     * 
     * @param int|null $storeId
     * @return int
     */
    public function getId(int $storeId = null): int;
}