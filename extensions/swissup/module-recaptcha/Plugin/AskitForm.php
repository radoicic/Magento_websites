<?php

namespace Swissup\Recaptcha\Plugin;

class AskitForm
{
    /**
     * @var \Swissup\Recaptcha\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Captcha\Helper\Data
     */
    private $captchaHelper;

    /**
     * @param \Swissup\Recaptcha\Helper\Data $helper
     * @param \Magento\Captcha\Helper\Data   $captchaHelper
     */
    public function __construct(
        \Swissup\Recaptcha\Helper\Data $helper,
        \Magento\Captcha\Helper\Data $captchaHelper
    ) {
        $this->helper = $helper;
        $this->captchaHelper = $captchaHelper;
    }

    /**
     * @param  \Swissup\Askit\Block\Question\AbstractForm $subject
     * @param  array                                      $result
     * @return array
     */
    public function afterGetCaptchaConfig(
        \Swissup\Askit\Block\Question\AbstractForm $subject,
        $result
    ) {
        $formId = $result['formId'] ?? '';
        if (!$this->helper->isEnabled() || !$formId) {
            return $result;
        }

        $config = $result;
        $recaptcha = $this->captchaHelper->getCaptcha($formId);
        $config['component'] = 'Swissup_Recaptcha/js/view/ui/recaptcha';
        $config['configSource'] = $recaptcha->getJsOptions();

        return $config;
    }
}
