<?php

namespace TiDesign\Fundraiser\Plugin\Magento\Framework\View\Layout;

use TiDesign\Fundraiser\Model\Catalog\Category\IsFundraiserCategories;
use TiDesign\Fundraiser\Model\Catalog\GetCurrentCategoryIdsByRequest;

class FundraiserCategoryCacheablePlugin
{
    /**
     * @param GetCurrentCategoryIdsByRequest $getCurrentCategoryIdsByRequest
     * @param IsFundraiserCategories $isFundraiserCategories
     */
    public function __construct(
        protected GetCurrentCategoryIdsByRequest $getCurrentCategoryIdsByRequest,
        protected IsFundraiserCategories $isFundraiserCategories
    ) {
    }

    /**
     * @param \Magento\Framework\View\Layout $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsCacheable($subject, $result)
    {
        if ($result && ($categoryIds = $this->getCurrentCategoryIdsByRequest->execute())) {
            $isFundraiserCategories = $this->isFundraiserCategories->execute($categoryIds);
            return !in_array(true, array_values($isFundraiserCategories));
        }
        return $result;
    }
}
