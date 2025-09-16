<?php

namespace TiDesign\Videobackground\Model\Source;

class Sourcelist implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 0, 'label' => __('Youtube Video')], ['value' => 1, 'label' => __('Local Video')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('Youtube Video'), 1 => __('Local Video')];
    }
}