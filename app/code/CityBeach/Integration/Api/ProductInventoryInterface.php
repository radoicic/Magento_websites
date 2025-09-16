<?php

namespace CityBeach\Integration\Api;

interface ProductInventoryInterface
{
    /**
     * Retrieve product inventory for products matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
