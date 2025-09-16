<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Catalog\Product;

/**
 * Product cart configuration plugin
 */
class CartConfiguration
{
    /**
     * Around is product configured
     *
     * @param \Magento\Catalog\Model\Product\CartConfiguration $subject
     * @param callable $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @param array $config
     * @return bool
     */
    public function aroundIsProductConfigured(
        \Magento\Catalog\Model\Product\CartConfiguration $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product,
        $config
    )
    {
        if (!isset($config['options']) && !$product->getRequiredOptions()) {
            $config['options'] = [];
        }
        return $proceed($product, $config) && !empty($config['source']);
    }
}