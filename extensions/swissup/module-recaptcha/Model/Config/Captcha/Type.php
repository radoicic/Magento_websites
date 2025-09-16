<?php

namespace Swissup\Recaptcha\Model\Config\Captcha;

class Type implements \Magento\Framework\Option\ArrayInterface
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
                'value' => 'recaptcha',
                'label' => __('Yes')
            ],
            [
                'value' => 'default',
                'label' => __('No')
            ]
        ];
    }
}
