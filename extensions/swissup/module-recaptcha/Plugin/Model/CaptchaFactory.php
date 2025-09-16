<?php

namespace Swissup\Recaptcha\Plugin\Model;

class CaptchaFactory
{

    /**
     * @var \Swissup\Recaptcha\Model\DefaultModelFactory
     */
    protected $factory;

    /**
     * @var \Swissup\Recaptcha\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Swissup\Recaptcha\Helper\Data $recaptchaHelper,
        \Swissup\Recaptcha\Model\RecaptchaFactory $recaptchaFactory
    ){
        $this->helper = $recaptchaHelper;
        $this->factory = $recaptchaFactory;
    }    

    /**
     * Get Recaptcha instance
     *
     * @param \Magento\Captcha\Model\CaptchaFactory $subject
     * @param string $captchaType
     * @param string $formId
     * @return \Magento\Captcha\Model\CaptchaInterface
     */
    public function aroundCreate(
        \Magento\Captcha\Model\CaptchaFactory $subject,
        callable $proceed,
        $captchaType,
        $formId
    ){
        if ($this->helper->isEnabled()) {
            $instance = $this->factory->create(['formId' => $formId]);
            return $instance;
        }
        return $proceed($captchaType, $formId);
    }

}
