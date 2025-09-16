<?php

namespace TiDesign\Fundraiser\Model\Catalog\Product;

use TiDesign\Fundraiser\Model\Catalog\Category\CategoryIdsToArrayProcessor;

class GetCategoryIdsByProduct
{
    /**
     * @param CategoryIdsToArrayProcessor $categoryIdsToArrayProcessor
     */
    public function __construct(
        protected CategoryIdsToArrayProcessor $categoryIdsToArrayProcessor
    ) {
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return int[]
     */
    public function execute($product)
    {
        if (!$product || !$product->getId()) {
            return [];
        }
        $categoryIds = $product->getCategoryIds();
        return $this->categoryIdsToArrayProcessor->execute($categoryIds);
    }
}
