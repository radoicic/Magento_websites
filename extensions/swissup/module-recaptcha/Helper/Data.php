<?php

namespace Swissup\Recaptcha\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * __construct
     *
     * @param \Magento\Framework\App\Helper\Context $context [description]
     * @param \Magento\Framework\App\State          $state   [description]
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->appState = $state;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Get field name of recaptcha respone in form submit data
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'g-recaptcha-response';
    }

    /**
     * Get recaptcha response value from request
     *
     * @return string
     */
    public function getGoogleRerponse()
    {
        $response = $this->_getRequest()->getPost($this->getFieldName());
        if (!$response && $this->_getRequest()->isXmlHttpRequest()) {
            $content = $this->_getRequest()->getContent();
            if ($content) {
                $params = json_decode($content, true);
                if (isset($params[$this->getFieldName()])) {
                    $response = $params[$this->getFieldName()];
                }
            }
        }

        return $response;
    }

    /**
     * Check if recaptcha enabled for areaCode
     *
     * @param  string  $areaCode
     * @return boolean
     */
    public function isEnabled($areaCode = null)
    {
        if (!isset($areaCode)) {
            $areaCode = $this->appState->getAreaCode();
        }

        if ($areaCode === 'webapi_rest') {
            // Request from checkout page has 'webapi_rest' area code.
            $areaCode = 'frontend';
        }

        return $this->scopeConfig->isSetFlag(
                "swissup_recaptcha/general/enable_{$areaCode}",
                ScopeInterface::SCOPE_STORE
            );
    }

    /**
     * Get config value by key
     *
     * @param  string $key
     * @return string
     */
    public function getConfig($key)
    {
        return $this->scopeConfig->getValue(
            $key,
            ScopeInterface::SCOPE_STORE,
            null
        );
    }

    /**
     * Is Magento Captcha disabled because Recaptcha enabled
     *
     * @return boolean
     */
    public function isMagentoCaptchaDisabled()
    {
        return $this->isEnabled();
    }

    /**
     * Is Recaptcha disabled
     *
     * @return boolean
     */
    public function isDisabled()
    {
        return !$this->isEnabled();
    }

    /**
     * Is Recaptcha at Checkout. Also depends is user in guest or sighen in.
     *
     * @return boolean
     */
    public function isDisabledAtCheckout()
    {
        if ($this->isEnabled()) {
            $formId = $this->getCheckoutFormId();

            return !$this->scopeConfig->isSetFlag(
                "swissup_recaptcha/general/protect_{$formId}",
                ScopeInterface::SCOPE_STORE
            );
        }

        return true;
    }

    /**
     * @return string
     */
    public function getCheckoutFormId()
    {
        return $this->customerSession->isLoggedIn()
            ? 'checkout_payments_signedin'
            : 'checkout_payments_guest';
    }
}
