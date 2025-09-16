<?php

namespace Swissup\Recaptcha\Model\Checkout;

class ConfigProvider extends \Magento\Captcha\Model\Checkout\ConfigProvider
{
    protected $recaptchaHelper;
    /**
     * @param \Swissup\Recaptcha\Helper\Data             $recaptchaHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Captcha\Helper\Data               $captchaData
     * @param array                                      $formIds
     */
    public function __construct(
        \Swissup\Recaptcha\Helper\Data $recaptchaHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Captcha\Helper\Data $captchaData,
        array $formIds
    ) {
        $this->recaptchaHelper = $recaptchaHelper;
        if ($this->recaptchaHelper->isEnabled()) {
            $formIds += [
                // Add custom forms for recaptcha.
                'checkout_payments_guest' => 'checkout_payments_guest',
                'checkout_payments_signedin' => 'checkout_payments_signedin'
            ];
        }

        parent::__construct($storeManager, $captchaData, $formIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        if (!$this->recaptchaHelper->isEnabled()) {
            return parent::getConfig();
        }

        $config = [];
        foreach ($this->formIds as $formId) {
            $model = $this->getCaptchaModel($formId);
            if ($model->isRequired()) {
                $config['swissupRecaptcha'][$formId] = $model->getJsOptions();
            }
        }

        return $config;
    }
}
