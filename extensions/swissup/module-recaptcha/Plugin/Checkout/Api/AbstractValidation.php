<?php

namespace Swissup\Recaptcha\Plugin\Checkout\Api;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Framework\Exception\CouldNotSaveException;

abstract class AbstractValidation
{
    /**
     * @var string
     */
    protected $formId = '';

    /**
     * @var \Magento\Captcha\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    /**
     * @param \Magento\Captcha\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $session
     */
    public function __construct(
        \Magento\Captcha\Helper\Data $helper,
        \Magento\Checkout\Model\Session $session
    ) {
        $this->helper = $helper;
        $this->session = $session;
    }

    /**
     * @param  PaymentInterface $paymentMethod
     * @throws CouldNotSaveException
     */
    protected function validateCaptcha(PaymentInterface $paymentMethod)
    {
        $captcha = $this->helper->getCaptcha($this->formId);
        if ($captcha->isRequired() && !$this->isSuccessValidation()) {
            $gResponse = $paymentMethod->getExtensionAttributes() === null
                ? ''
                : $paymentMethod->getExtensionAttributes()->getSwissupRecaptchaResponse();
            $response = $captcha->verify($gResponse);
            if (!$response->isSuccess()) {
                $errors = implode(', ', $response->getErrorCodes());
                throw new CouldNotSaveException(
                    __("The order wasn't placed. Invalid recaptcha. [error-code: %1]", $errors)
                );
            }

            $this->setSuccessValidation(true);
        }
    }

    /**
     * Is captcha already validated and is success.
     *
     * @return boolean
     */
    private function isSuccessValidation()
    {
        $endTime = $this->session->getData("swissup_recaptcha/{$this->formId}/end_time");
        $isValid = !!$this->session->getData("swissup_recaptcha/{$this->formId}/is_valid");
        if ($endTime >= time()) {
            return $isValid;
        }

        return false;
    }

    /**
     * Set success validation flag.
     *
     * @param boolean $isValid
     */
    private function setSuccessValidation($isValid)
    {
        $minutes = 2; //Each reCAPTCHA user response token is valid for two minutes,
        // and can only be verified once to prevent replay attacks.
        // https://developers.google.com/recaptcha/docs/verify#token_restrictions
        $this->session->setData('swissup_recaptcha', [
            $this->formId => [
                'end_time' => time() + $minutes * 60,
                'is_valid' => $isValid
            ]
        ]);
    }
}
