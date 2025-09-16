<?php

namespace TiDesign\Fundraiser\Model\Catalog\Category;

class IsFundraiserCategories
{
    public function __construct(
        protected GetFundraiserCategoryIds $getFundraiserCategoryIds
    ) {
    }

    /**
     * @param array $categoryIds
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function execute($categoryIds = [])
    {
        if (!$categoryIds) {
            return [];
        }
        $fundraiserCategoryIds = $this->getFundraiserCategoryIds->execute();
        $result = [];
        foreach ($categoryIds as $categoryId) {
            $result[$categoryId] = isset($fundraiserCategoryIds[$categoryId]);
        }
        return $result;
    }
}
