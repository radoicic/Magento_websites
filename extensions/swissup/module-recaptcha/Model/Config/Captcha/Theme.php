<?php

namespace Swissup\Recaptcha\Model\Config\Captcha;

class Theme implements \Magento\Framework\Option\ArrayInterface
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
                'value' => 'dark',
                'label' => __('Dark')
            ],
            [
                'value' => 'light',
                'label' => __('Light')
            ]
        ];
    }
}
