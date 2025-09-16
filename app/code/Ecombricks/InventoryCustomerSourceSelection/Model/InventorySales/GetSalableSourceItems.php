<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales;

/**
 * Get salable source items
 */
class GetSalableSourceItems
{
    /**
     * Get source items
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetSourceItems
     */
    private $getSourceItems;

    /**
     * Source items
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\SourceItemInterface[][]
     */
    private $sourceItems = [];

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetSourceItems $getSourceItems
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetSourceItems $getSourceItems
    )
    {
        $this->getSourceItems = $getSourceItems;
    }

    /**
     * Execute
     * 
     * @param string $sku
     * @param int $stockId
     * @return \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\SourceItemInterface[]
     */
    public function execute(string $sku, int $stockId): array
    {
        if (
            array_key_exists($sku, $this->sourceItems) && 
            array_key_exists($stockId, $this->sourceItems[$sku])
        ) {
            return $this->sourceItems[$sku][$stockId];
        }
        $sourceItems = $this->getSourceItems->execute($sku, $stockId);
        $salableSourceItems = [];
        foreach ($sourceItems as $sourceCode => $sourceItem) {
            if ($sourceItem->isSalable()) {
                $salableSourceItems[$sourceCode] = $sourceItem;
            }
        }
        return $this->sourceItems[$sku][$stockId] = $salableSourceItems;
    }
}