<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model;

/**
 * Get stock ID by source code
 */
class GetStockIdBySourceCode
{
    /**
     * Search criteria builder
     * 
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Get stock source links
     * 
     * @var \Magento\InventoryApi\Api\GetStockSourceLinksInterface
     */
    private $getStockSourceLinks;

    /**
     * Default stock provider
     * 
     * @var \Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface
     */
    private $defaultStockProvider;

    /**
     * Stock IDs
     * 
     * @var array
     */
    private $stockIds = [];

    /**
     * Constructor
     * 
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\InventoryApi\Api\GetStockSourceLinksInterface $getStockSourceLinks
     * @param \Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface $defaultStockProvider
     * @return void
     */
    public function __construct(
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\InventoryApi\Api\GetStockSourceLinksInterface $getStockSourceLinks,
        \Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface $defaultStockProvider
    )
    {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->getStockSourceLinks = $getStockSourceLinks;
        $this->defaultStockProvider = $defaultStockProvider;
    }

    /**
     * Execute
     * 
     * @param string $sourceCode
     * @return int
     */
    public function execute(string $sourceCode): int
    {
        if (array_key_exists($sourceCode, $this->stockIds)) {
            return $this->stockIds[$sourceCode];
        }
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(\Magento\InventoryApi\Api\Data\StockSourceLinkInterface::SOURCE_CODE, $sourceCode)
            ->create();
        $stockSourceLinks = $this->getStockSourceLinks->execute($searchCriteria)->getItems();
        if (!empty($stockSourceLinks)) {
            $stockSourceLink = current($stockSourceLinks);
            $stockId = (int) $stockSourceLink->getStockId();
        } else {
            $stockId = (int) $this->defaultStockProvider->getId();
        }
        return $this->stockIds[$sourceCode] = $stockId;
    }
}