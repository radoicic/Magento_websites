<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventorySourceSelection\Algorithms\Result;

/**
 * Get default sorted sources result plugin
 */
class GetDefaultSortedSourcesResult
{
    /**
     * Order item repository
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySourceSelection\Algorithms\Result\GetDefaultSortedSourcesResult
     */
    private $getDefaultSortedSourcesResult;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySourceSelection\Algorithms\Result\GetDefaultSortedSourcesResult $getDefaultSortedSourcesResult
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySourceSelection\Algorithms\Result\GetDefaultSortedSourcesResult $getDefaultSortedSourcesResult
    )
    {
        $this->getDefaultSortedSourcesResult = $getDefaultSortedSourcesResult;
    }
    
    /**
     * Around execute
     * 
     * @param \Magento\InventorySourceSelectionApi\Model\Algorithms\Result\GetDefaultSortedSourcesResult
     * @param \Closure $proceed
     * @param \Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterface $inventoryRequest
     * @param \Magento\InventoryApi\Api\Data\SourceInterface[] $sortedSources
     * @return \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterface
     */
    public function aroundExecute(
        \Magento\InventorySourceSelectionApi\Model\Algorithms\Result\GetDefaultSortedSourcesResult $subject,
        \Closure $proceed,
        \Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterface $inventoryRequest,
        array $sortedSources
    ): \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterface
    {
        return $this->getDefaultSortedSourcesResult->execute($inventoryRequest, $sortedSources);
    }
}