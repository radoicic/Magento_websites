<?php

namespace Swissup\Recaptcha\Model;

use Magento\Captcha\Model\DefaultModel;

class Recaptcha extends DefaultModel
{
    /**
     * @var \Swissup\Recaptcha\Helper\Data
     */
    protected $helper;

    /**
     * @var \ReCaptcha\ReCaptcha[]
     */
    protected $response = [];

    /**
     * @var array
     */
    private $designException;

    /**
     * @param \Magento\Framework\Session\SessionManagerInterface $session
     * @param \Magento\Captcha\Helper\Data $captchaData
     * @param \Magento\Captcha\Model\ResourceModel\LogFactory $resLogFactory
     * @param string $formId
     * @param \Swissup\Recaptcha\Model\Config\Backend\DesignException $configValueProcessor
     * @param \Swissup\Recaptcha\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Captcha\Helper\Data $captchaData,
        \Magento\Captcha\Model\ResourceModel\LogFactory $resLogFactory,
        $formId,
        \Swissup\Recaptcha\Model\Config\Backend\DesignException $configValueProcessor,
        \Swissup\Recaptcha\Helper\Data $helper
    ) {
        $this->configValueProcessor = $configValueProcessor;
        $this->helper = $helper;
        parent::__construct($session, $captchaData, $resLogFactory, $formId);
        // Fix for Magento 2.1.x
        $this->session = $session;
        $this->formId = $formId;

        // Initialize previos responses from session.
        // Checkout sends multiple requests with same recaptchas.
        // Recaptcha can be validated only once.
        $this->initializeResponse();
        $this->saveResponse();
    }

    /**
     * Generate captcha
     *
     * @return string captcha ID
     */
    public function generate()
    {
        // leave this method empty to prevent usless image generating
        $id = -1;
        return $id;
    }

    /**
     * Return full URL to captcha image
     *
     * @return string
     */
    public function getImgSrc()
    {
        return '';
    }

    /**
     * Checks whether captcha was guessed correctly by user
     *
     * @param string $word / for compatibility with Zend_Captcha
     * @return bool
     */
    public function isCorrect($word)
    {
        $gResponse = $this->helper->getGoogleRerponse();
        return $this->verify($gResponse)->isSuccess();
    }

    /**
     * Verify recapctha response.
     *
     * @param  string $gResponse
     * @return \ReCaptcha\Response
     */
    public function verify($gResponse) {
        if (!isset($this->response[$gResponse])) {
            // Prevent multiple recaptcha verifications.
            // I causes invalidate captcha.
            $recaptcha = new \ReCaptcha\ReCaptcha(
                $this->getSecretKey(),
                $this->getRequestMethod()
            );
            $this->response[$gResponse] = $recaptcha->verify($gResponse);
            // Save verified reapctha into session
            $this->saveResponse();
        }

        return $this->response[$gResponse];
    }

    /**
     * Get Block Name
     *
     * @return string
     */
    public function getBlockName()
    {
        return 'Swissup\Recaptcha\Block\Recaptcha';
    }

    /**
     * @return string
     */
    public function getSiteKey()
    {
        return $this->helper->getConfig('swissup_recaptcha/api_key/site_key');
    }

    /**
     * Get RaCAPTCHA them value from config
     *
     * @return string
     */
    public function getTheme()
    {
        return $this->getConfig('theme');
    }

    /**
     * Get RaCAPTCHA size value from config
     *
     * @return string
     */
    public function getSize()
    {
        return $this->getConfig('size');
    }

    /**
     * Get ReCAPTCHA type (v2|invisible)
     *
     * @return string
     */
    public function getRtype()
    {
        return $this->getConfig('rtype');
    }

    /**
     * @return string
     */
    protected function getSecretKey()
    {
        return $this->helper->getConfig('swissup_recaptcha/api_key/secret_key');
    }

    /**
     * Get badge position (works with Invisible reCAPTCHA only)
     *
     * @return string
     */
    public function getBadge()
    {
        if ($this->getRtype() == 'invisible') {
            return $this->getConfig('badge');
        }

        return '';
    }

    /**
     * Get options for javascript widget
     *
     * @return array
     */
    public function getJsOptions()
    {
        return ($this->getRtype() == 'invisible'
                ? [
                    'size' => 'invisible',
                    'badge' => $this->getBadge(),
                    'minHeight' => '60px',
                    'hookFormSubmit' => true
                ]
                : []
            )
            + [
                'sitekey' => $this->getSiteKey(),
                'theme' => $this->getTheme(),
                'size' => $this->getSize()
            ];
    }

    /**
     * Get request method to communicate with recaptcha service
     *
     * @return \ReCaptcha\RequestMethod
     */
    private function getRequestMethod()
    {
        $method = $this->helper->getConfig(
            'swissup_recaptcha/general/request_method'
        );
        $className = '\ReCaptcha\RequestMethod\\' . $method;
        return new $className;
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired($login = null)
    {
        if ($this->helper->getConfig("swissup_recaptcha/general/protect_{$this->formId}")
            && $this->helper->isEnabled()
        ) {
            return true;
        }

        return parent::isRequired($login);
    }

    /**
     * Get recaptcha design config respecting design exception.
     *
     * @param  string $config
     * @return string
     */
    private function getConfig($config)
    {
        if (is_null($this->designException)) {
            $configValue = $this->helper->getConfig(
                'swissup_recaptcha/design/design_exception'
            );
            $this->designException = $this->configValueProcessor
                ->makeArrayFieldValue($configValue);
        }

        foreach ($this->designException as $exception) {
            if ($exception['captcha_form'] === $this->formId) {
                return $exception[$config] ?? '';
            }
        }

        return $this->helper->getConfig("swissup_recaptcha/design/{$config}");
    }

    /**
     * Initialize recaptcha responses from session
     */
    private function initializeResponse()
    {
        $responses = $this->session->getData('swissup_recaptcha_response');
        $this->response = [];
        if (is_array($responses)) {
            $now = time();
            foreach ($responses as $gResponse => $json) {
                $this->response[$gResponse] = \ReCaptcha\Response::fromJson($json);
                $challengeTs = $this->response[$gResponse]->getChallengeTs();
                $expiresAt = strtotime($challengeTs ?: '') + 5 * 60;
                if ($now > $expiresAt) {
                    unset($this->response[$gResponse]);
                }
            }
        }
    }

    /**
     * Save response into session
     */
    private function saveResponse()
    {
        $responses = [];
        foreach ($this->response as $gResponse => $response) {
            $responses[$gResponse] = json_encode($response->toArray());
        }

        $this->session->setData('swissup_recaptcha_response', $responses);
    }
}
