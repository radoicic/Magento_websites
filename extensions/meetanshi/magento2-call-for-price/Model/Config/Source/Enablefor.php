<?php

namespace Meetanshi\Callforprice\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Enablefor
 */
class Enablefor implements ArrayInterface
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'global', 'label' => __('Global')],
            ['value' => 'category', 'label' => __('Category Specific')],
            ['value' => 'product', 'label' => __('Product Specific')]
        ];
    }
}
