<?php

namespace CityBeach\Integration\Api;

interface ProductSummaryInterface
{
    /**
     * Retrieve product summary for products matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param string $sourceCode
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $sourceCode);

    /**
     * Retrieve product inventory for available sources for products matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param string $sourceCode
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSourceInventoryList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $sourceCode);

    /**
     * Count product summary for products matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCount(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
