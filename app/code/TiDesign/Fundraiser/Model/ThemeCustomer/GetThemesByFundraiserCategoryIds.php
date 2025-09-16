<?php

namespace TiDesign\Fundraiser\Model\ThemeCustomer;

use TiDesign\Fundraiser\Model\ResourceModel\ThemeCustomer\CollectionFactory;

class GetThemesByFundraiserCategoryIds
{
    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        protected CollectionFactory $collectionFactory
    ) {
    }

    /**
     * @param int[] $categoryIds
     * @return \TiDesign\Fundraiser\Model\ThemeCustomer
     */
    public function execute($categoryIds)
    {
        if (!$categoryIds) {
            return [];
        }
        /** @var \TiDesign\Fundraiser\Model\ResourceModel\ThemeCustomer\Collection $collection */
        $collection = $this->collectionFactory->create();
        $condition = array_map(fn($catId) => new \Zend_Db_Expr("FIND_IN_SET($catId, category_id)"), $categoryIds);
        $condition = implode(' OR ', $condition);
        $collection->getSelect()->where(new \Zend_Db_Expr("($condition)"));
        return $collection->getItems();
    }
}
