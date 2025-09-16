<?php

namespace Swissup\Recaptcha\Model\Config\Captcha;

class Size implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'compact',
                'label' => __('Compact')
            ],
            [
                'value' => 'normal',
                'label' => __('Normal')
            ]
        ];
    }
}
