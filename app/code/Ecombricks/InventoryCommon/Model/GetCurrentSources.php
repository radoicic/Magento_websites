<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model;

/**
 * Get current sources
 */
class GetCurrentSources
{
    /**
     * Current stock provider
     * 
     * @var \Ecombricks\InventoryCommon\Api\CurrentStockProviderInterface
     */
    private $currentStockProvider;

    /**
     * Get enabled sources by stock ID
     * 
     * @var \Ecombricks\InventoryCommon\Model\GetEnabledSourcesByStockId 
     */
    private $getEnabledSourcesByStockId;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Api\CurrentStockProviderInterface $currentStockProvider
     * @param \Ecombricks\InventoryCommon\Model\GetEnabledSourcesByStockId $getEnabledSourcesByStockId
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Api\CurrentStockProviderInterface $currentStockProvider,
        \Ecombricks\InventoryCommon\Model\GetEnabledSourcesByStockId $getEnabledSourcesByStockId
    )
    {
        $this->currentStockProvider = $currentStockProvider;
        $this->getEnabledSourcesByStockId = $getEnabledSourcesByStockId;
    }
    
    /**
     * Execute
     * 
     * @param int|null $storeId
     * @return \Magento\InventoryApi\Api\Data\SourceInterface[]
     */
    public function execute(int $storeId = null): array
    {
        return $this->getEnabledSourcesByStockId->execute($this->currentStockProvider->getId($storeId));
    }
}