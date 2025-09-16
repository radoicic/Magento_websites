<?php

namespace Swissup\Recaptcha\Model\Config\Captcha;

class Rtype implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'v2',
                'label' => __('reCAPTCHA v2')
            ],
            [
                'value' => 'invisible',
                'label' => __('Invisible reCAPTCHA')
            ]
        ];
    }
}
