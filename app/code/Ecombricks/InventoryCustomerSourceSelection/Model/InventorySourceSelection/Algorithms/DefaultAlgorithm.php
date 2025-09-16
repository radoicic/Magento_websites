<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventorySourceSelection\Algorithms;

/**
 * Default source selection algorithm
 */
class DefaultAlgorithm implements \Magento\InventorySourceSelectionApi\Model\SourceSelectionInterface
{
    /**
     * Get default sorted sources result
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySourceSelection\Algorithms\Result\GetDefaultSortedSourcesResult 
     */
    private $getDefaultSortedSourcesResult;

    /**
     * Get sources assigned to stock ordered by priority
     * 
     * @var \Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface
     */
    private $getSourcesAssignedToStockOrderedByPriority;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySourceSelection\Algorithms\Result\GetDefaultSortedSourcesResult $getDefaultSortedSourcesResult
     * @param \Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface $getSourcesAssignedToStockOrderedByPriority
     * @retun void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySourceSelection\Algorithms\Result\GetDefaultSortedSourcesResult $getDefaultSortedSourcesResult,
        \Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface $getSourcesAssignedToStockOrderedByPriority
    )
    {
        $this->getDefaultSortedSourcesResult = $getDefaultSortedSourcesResult;
        $this->getSourcesAssignedToStockOrderedByPriority = $getSourcesAssignedToStockOrderedByPriority;
    }

    /**
     * Execute
     * 
     * @param \Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterface $inventoryRequest
     * @return \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterface
     */
    public function execute(
        \Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterface $inventoryRequest
    ): \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterface
    {
        $sortedSources = $this->getEnabledSourcesOrderedByPriority($inventoryRequest->getStockId());
        $inventoryRequestExtension = $inventoryRequest->getExtensionAttributes();
        $inventoryRequestExtension->setIsDefault(true);
        $sourceSelectionResult = $this->getDefaultSortedSourcesResult->execute($inventoryRequest, $sortedSources);
        $inventoryRequestExtension->setIsDefault(false);
        return $sourceSelectionResult;
    }

    /**
     * Get enabled sources ordered by priority
     *
     * @param int $stockId
     * @return \Magento\InventoryApi\Api\Data\SourceInterface[]
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getEnabledSourcesOrderedByPriority(int $stockId): array
    {
        return array_filter(
            $this->getSourcesAssignedToStockOrderedByPriority->execute($stockId),
            function (\Magento\InventoryApi\Api\Data\SourceInterface $source) {
                return $source->isEnabled();
            }
        );
    }
}