<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model;

/**
 * Get stock source links by stock ID
 */
class GetStockSourceLinksByStockId
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
     * Get stock source links
     * 
     * @var \Magento\InventoryApi\Api\GetStockSourceLinksInterface
     */
    private $getStockSourceLinks;

    /**
     * Stock source links
     * 
     * @var array
     */
    private $stockSourceLinks = [];

    /**
     * Constructor
     * 
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder
     * @param \Magento\InventoryApi\Api\GetStockSourceLinksInterface $getStockSourceLinks
     * @return void
     */
    public function __construct(
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder,
        \Magento\InventoryApi\Api\GetStockSourceLinksInterface $getStockSourceLinks
    )
    {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->getStockSourceLinks = $getStockSourceLinks;
    }

    /**
     * Execute
     * 
     * @param int $stockId
     * @return \Magento\InventoryApi\Api\Data\StockSourceLinkInterface[]
     */
    public function execute(int $stockId): array
    {
        if (array_key_exists($stockId, $this->stockSourceLinks)) {
            return $this->stockSourceLinks[$stockId];
        }
        $sortOrder = $this->sortOrderBuilder
            ->setField(\Magento\InventoryApi\Api\Data\StockSourceLinkInterface::PRIORITY)
            ->setAscendingDirection()
            ->create();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(\Magento\InventoryApi\Api\Data\StockSourceLinkInterface::STOCK_ID, $stockId)
            ->addSortOrder($sortOrder)
            ->create();
        $stockSourceLinks = [];
        foreach ($this->getStockSourceLinks->execute($searchCriteria)->getItems() as $stockSourceLink) {
            $stockSourceLinks[$stockSourceLink->getSourceCode()] = $stockSourceLink;
        }
        return $this->stockSourceLinks[$stockId] = $stockSourceLinks;
    }
}