<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model;

/**
 * Get stock by store
 */
class GetStockByStore implements \Ecombricks\InventoryCommon\Api\GetStockByStoreInterface
{
    /**
     * Get default stock
     * 
     * @var \Ecombricks\InventoryCommon\Api\GetDefaultStockInterface
     */
    private $getDefaultStock;

    /**
     * Stock resolver
     * 
     * @var \Magento\InventorySalesApi\Api\StockResolverInterface
     */
    private $stockResolver;

    /**
     * Store manager
     * 
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Api\GetDefaultStockInterface $getDefaultStock
     * @param \Magento\InventorySalesApi\Api\StockResolverInterface $stockResolver
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Api\GetDefaultStockInterface $getDefaultStock,
        \Magento\InventorySalesApi\Api\StockResolverInterface $stockResolver,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->getDefaultStock = $getDefaultStock;
        $this->stockResolver = $stockResolver;
        $this->storeManager = $storeManager;
    }

    /**
     * Execute
     * 
     * @param null|string|bool|int|\Magento\Store\Api\Data\StoreInterface $store
     * @return \Magento\InventoryApi\Api\Data\StockInterface
     */
    public function execute($store = null): \Magento\InventoryApi\Api\Data\StockInterface
    {
        try {
            $stock = $this->stockResolver->execute(
                \Magento\InventorySalesApi\Api\Data\SalesChannelInterface::TYPE_WEBSITE,
                $this->storeManager->getStore($store)->getWebsite()->getCode()
            );
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            $stock = $this->getDefaultStock->execute();
        }
        return $stock;
    }
}