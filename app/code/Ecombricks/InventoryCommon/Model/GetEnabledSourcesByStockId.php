<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model;

/**
 * Get enabled sources by stock ID
 */
class GetEnabledSourcesByStockId
{
    /**
     * Get sources by stock ID
     * 
     * @var \Ecombricks\InventoryCommon\Model\GetSourcesByStockId 
     */
    private $getSourcesByStockId;

    /**
     * Sources
     * 
     * @var array
     */
    private $sources = [];

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Model\GetSourcesByStockId $getSourcesByStockId
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Model\GetSourcesByStockId $getSourcesByStockId
    )
    {
        $this->getSourcesByStockId = $getSourcesByStockId;
    }
    
    /**
     * Execute
     * 
     * @param int $stockId
     * @return \Magento\InventoryApi\Api\Data\SourceInterface[]
     */
    public function execute(int $stockId): array
    {
        if (array_key_exists($stockId, $this->sources)) {
            return $this->sources[$stockId];
        }
        $sources = [];
        foreach ($this->getSourcesByStockId->execute($stockId) as $sourceCode => $source) {
            if (!$source->isEnabled()) {
                continue;
            }
            $sources[$sourceCode] = $source;
        }
        return $this->sources[$stockId] = $sources;
    }
}