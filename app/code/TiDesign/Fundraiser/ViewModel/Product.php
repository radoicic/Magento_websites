<?php

namespace TiDesign\Fundraiser\ViewModel;

use Magento\Catalog\Helper\Product as ProductHelper;

class Product implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @param ProductHelper $productHelper
     */
    public function __construct(
        protected ProductHelper $productHelper
    ) {
    }

    /**
     * @param $product
     * @return bool|string
     */
    public function getProductImage($product)
    {
        return $this->productHelper->getImageUrl($product);
    }
}
