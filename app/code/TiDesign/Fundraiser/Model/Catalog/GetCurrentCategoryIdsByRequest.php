<?php

namespace TiDesign\Fundraiser\Model\Catalog;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use TiDesign\Fundraiser\Helper\Config as ConfigHelper;
use TiDesign\Fundraiser\Model\Catalog\Product\GetCategoryIdsByProduct;

class GetCurrentCategoryIdsByRequest
{
    const VALID_FULL_ACTION_NAMES = [
        'catalog_category_view',
        'catalog_product_view',
        'weltpixel_quickview_catalog_product_view'
    ];

    /**
     * @param ConfigHelper $configHelper
     * @param RequestInterface $request
     * @param Registry $registry
     * @param GetCategoryIdsByProduct $getCategoryIdsByProduct
     */
    public function __construct(
        protected ConfigHelper $configHelper,
        protected RequestInterface $request,
        protected Registry $registry,
        protected GetCategoryIdsByProduct $getCategoryIdsByProduct
    ) {
    }

    /**
     * @return int[]|null
     */
    public function execute()
    {
        if (!$this->isValidRequest()) {
            return [];
        }
        $category = $this->registry->registry('current_category');
        if ($category && ($categoryId = $category->getId())) {
            return [$categoryId];
        }
        $product = $this->registry->registry('current_product');
        return $this->getCategoryIdsByProduct->execute($product);
    }

    /**
     * @return bool
     */
    private function isValidRequest()
    {
        if (!$this->configHelper->isEnabled()) {
            return false;
        }
        $fullActionName = $this->request->getFullActionName() ?: '';
        return in_array(strtolower($fullActionName), self::VALID_FULL_ACTION_NAMES);
    }
}
