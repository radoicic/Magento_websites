<?php

namespace Swissup\Recaptcha\Block\Adminhtml\Form\Field;

class CaptchaForm extends AbstractRenderer
{
    /**
     * @var array
     */
    protected $traditionalForms = [
        'user_create',
        'user_login',
        'user_forgotpassword',
        'contact_us'
    ];

    /**
     * @var array
     */
    protected $recaptchaForms = [
        'newsletter_subscription' => 'Newsletter Subscribtion',
        'checkout_payments_guest' => 'Checkout as guest',
        'checkout_payments_signedin' => 'Checkout as signed in'
    ];

    /**
     * @param \Magento\Framework\View\Element\Context     $context
     * @param \Magento\Captcha\Model\Config\Form\Frontend $source
     * @param array                                       $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Captcha\Model\Config\Form\Frontend $source,
        array $data = []
    ) {
        parent::__construct($context, $source, $data);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $traditional = [];
            $thirdParty = [];
            foreach ($this->source->toOptionArray() as $option) {
                if (in_array($option['value'], $this->traditionalForms)) {
                    $traditional[] = [
                        'value' => $option['value'],
                        'label' => addslashes($option['label'])
                    ];
                } else {
                    $thirdParty[] = [
                        'value' => $option['value'],
                        'label' => addslashes($option['label'])
                    ];
                }
            }

            $recaptcha = [];
            foreach ($this->recaptchaForms as $key => $value) {
                $recaptcha[] = [
                    'value' => $key,
                    'label' => addslashes(__($value))
                ];
            }

            $this->setOptions([
                [
                    'label' => __('Traditional Forms'),
                    'value' => $traditional,
                    'optgroup-name' => 'traditional_forms'
                ],
                [
                    'label' => __('Featured Forms'),
                    'value' => $recaptcha,
                    'optgroup-name' => 'recaptcha_forms'
                ],
                [
                    'label' => __('Third-Party Forms'),
                    'value' => $thirdParty,
                    'optgroup-name' => 'thirdparty_forms'
                ]
            ]);
        }

        return parent::_toHtml();
    }
}
