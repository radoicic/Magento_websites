<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model;

/**
 * Get stock ID by store
 */
class GetStockIdByStore implements \Ecombricks\InventoryCommon\Api\GetStockIdByStoreInterface
{
    /**
     * Stock resolver
     * 
     * @var \Ecombricks\InventoryCommon\Api\GetStockByStoreInterface
     */
    private $getStockByStore;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Api\GetStockByStoreInterface $getStockByStore
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Api\GetStockByStoreInterface $getStockByStore
    )
    {
        $this->getStockByStore = $getStockByStore;
    }
    
    /**
     * Execute
     * 
     * @param null|string|bool|int|\Magento\Store\Api\Data\StoreInterface $store
     * @return int
     */
    public function execute($store = null): int
    {
        return (int) $this->getStockByStore->execute($store)->getStockId();
    }
}