<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model;

/**
 * Current stock provider
 */
class CurrentStockProvider implements \Ecombricks\InventoryCommon\Api\CurrentStockProviderInterface
{
    /**
     * Store manager
     * 
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Stock resolver
     * 
     * @var \Magento\InventorySalesApi\Api\StockResolverInterface
     */
    private $stockResolver;

    /**
     * Constructor
     * 
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\InventorySalesApi\Api\StockResolverInterface $stockResolver
     * @return void
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\InventorySalesApi\Api\StockResolverInterface $stockResolver
    )
    {
        $this->storeManager = $storeManager;
        $this->stockResolver = $stockResolver;
    }

    /**
     * Get ID
     * 
     * @param int|null $storeId
     * @return int
     */
    public function getId(int $storeId = null): int
    {
        $store = $this->storeManager->getStore($storeId);
        $stock = $this->stockResolver->execute(
            \Magento\InventorySalesApi\Api\Data\SalesChannelInterface::TYPE_WEBSITE,
            $store->getWebsite()->getCode()
        );
        return $stock->getStockId();
    }
}