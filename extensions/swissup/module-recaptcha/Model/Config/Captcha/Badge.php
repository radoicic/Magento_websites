<?php

namespace Swissup\Recaptcha\Model\Config\Captcha;

class Badge implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'bottomright',
                'label' => __('Bottom right')
            ],
            [
                'value' => 'bottomleft',
                'label' => __('Bottom left')
            ],
            [
                'value' => 'inline',
                'label' => __('Inline')
            ]
        ];
    }
}
