<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales;

/**
 * Get current salable source items
 */
class GetCurrentSalableSourceItems
{
    /**
     * Current stock provider
     * 
     * @var \Ecombricks\InventoryCommon\Api\CurrentStockProviderInterface
     */
    protected $currentStockProvider;
    
    /**
     * Get salable source items
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetSalableSourceItems
     */
    private $getSalableSourceItems;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Api\CurrentStockProviderInterface $currentStockProvider
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetSalableSourceItems $getSalableSourceItems
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Api\CurrentStockProviderInterface $currentStockProvider,
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetSalableSourceItems $getSalableSourceItems
    )
    {
        $this->currentStockProvider = $currentStockProvider;
        $this->getSalableSourceItems = $getSalableSourceItems;
    }

    /**
     * Execute
     * 
     * @param string $sku
     * @param int|null $storeId
     * @return \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\SourceItemInterface[]
     */
    public function execute(string $sku, int $storeId = null): array
    {
        $stockId = $this->currentStockProvider->getId($storeId);
        return $this->getSalableSourceItems->execute($sku, $stockId);
    }
}