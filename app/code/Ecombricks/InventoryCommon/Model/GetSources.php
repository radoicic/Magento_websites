<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model;

/**
 * Get sources
 */
class GetSources
{
    /**
     * Search criteria builder
     * 
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Sort order builder
     * 
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    private $sortOrderBuilder;
    
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
    private $sources;

    /**
     * Constructor
     * 
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
     * @return void
     */
    public function __construct(
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
    )
    {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->sourceRepository = $sourceRepository;
    }
    
    /**
     * Execute
     * 
     * @return \Magento\InventoryApi\Api\Data\SourceInterface[]
     */
    public function execute(): array
    {
        if ($this->sources !== null) {
            return $this->sources;
        }
        $sortOrder = $this->sortOrderBuilder
            ->setField(\Magento\InventoryApi\Api\Data\SourceInterface::NAME)
            ->setAscendingDirection()
            ->create();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addSortOrder($sortOrder)
            ->create();
        $sources = [];
        foreach ($this->sourceRepository->getList($searchCriteria)->getItems() as $source) {
            $sources[$source->getSourceCode()] = $source;
        }
        return $this->sources = $sources;
    }
}