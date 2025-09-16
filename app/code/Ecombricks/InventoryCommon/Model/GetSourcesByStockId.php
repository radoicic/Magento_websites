<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model;

/**
 * Get sources by stock ID
 */
class GetSourcesByStockId
{
    /**
     * Get stock source links by stock ID
     * 
     * @var \Ecombricks\InventoryCommon\Model\GetStockSourceLinksByStockId 
     */
    private $getStockSourceLinksByStockId;

    /**
     * Search criteria builder
     * 
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Source repository
     * 
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    private $sourceRepository;

    /**
     * Sources
     * 
     * @var array
     */
    private $sources = [];

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Model\GetStockSourceLinksByStockId $getStockSourceLinksByStockId
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Model\GetStockSourceLinksByStockId $getStockSourceLinksByStockId,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
    )
    {
        $this->getStockSourceLinksByStockId = $getStockSourceLinksByStockId;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceRepository = $sourceRepository;
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
        $stockSourceLinks = $this->getStockSourceLinksByStockId->execute($stockId);
        if (empty($stockSourceLinks)) {
            return $this->sources[$stockId] = [];
        }
        $sourceCodes = array_keys($stockSourceLinks);
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(\Magento\InventoryApi\Api\Data\SourceInterface::SOURCE_CODE, $sourceCodes, 'in')
            ->create();
        $sources = [];
        foreach ($this->sourceRepository->getList($searchCriteria)->getItems() as $source) {
            $sources[$source->getSourceCode()] = $source;
        }
        $sourcesSorted = [];
        foreach ($sourceCodes as $sourceCode) {
            if (empty($sources[$sourceCode])) {
                continue;
            }
            $sourcesSorted[$sourceCode] = $sources[$sourceCode];
        }
        return $this->sources[$stockId] = $sourcesSorted;
    }
}