<?php

namespace CityBeach\Integration\Api;

interface ProductSummaryRepositoryInterface
{
    /**
     * Retrieve product summary for products matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \CityBeach\Integration\Model\ResourceModel\ProductSummary\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
