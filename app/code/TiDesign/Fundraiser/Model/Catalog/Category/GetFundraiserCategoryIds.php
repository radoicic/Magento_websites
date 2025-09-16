<?php

namespace TiDesign\Fundraiser\Model\Catalog\Category;

use TiDesign\Fundraiser\Model\ResourceModel\Catalog\Category\GetFundraiserCategoryIds as GetFundraiserCategoryIdsResource;

class GetFundraiserCategoryIds
{
    protected $fundraiserCategoryIds = [];

    /**
     * @param GetFundraiserCategoryIdsResource $getFundraiserCategoryIdsResource
     */
    public function __construct(
        protected GetFundraiserCategoryIdsResource $getFundraiserCategoryIdsResource
    ) {
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function execute()
    {
        if (!$this->fundraiserCategoryIds) {
            $this->fundraiserCategoryIds = $this->getFundraiserCategoryIdsResource->execute();
        }
        return $this->fundraiserCategoryIds;
    }
}
