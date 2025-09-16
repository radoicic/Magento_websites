<?php

namespace Swissup\Recaptcha\Block;

class Recaptcha extends \Magento\Framework\View\Element\Template
{
    /**
     * Captcha data
     *
     * @var \Magento\Captcha\Helper\Data
     */
    protected $_captchaData = null;

    /**
     * @var string
     */
    protected $_template = 'Swissup_Recaptcha::recaptcha/recaptcha.phtml';

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Captcha\Helper\Data $captchaData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Captcha\Helper\Data $captchaData,
        array $data = []
    ) {
        $this->_captchaData = $captchaData;
        parent::__construct($context, $data);
    }

    /**
     * Returns captcha model
     *
     * @return \Swissup\Recaptcha\Model\Recaptcha
     */
    public function getCaptchaModel()
    {
        return $this->_captchaData->getCaptcha($this->getFormId());
    }

    /**
     * Get options tio initialize JS widget
     *
     * @param  string $format
     * @return string|array
     */
    public function getOptions($format = 'json')
    {
        $imageUrl = $this->getViewFileUrl('images/loader-2.gif');
        $options = ['imageLoader' => $this->escapeUrl($imageUrl)]
            + $this->getCaptchaModel()->getJsOptions();

        if ($format == 'json') {
            return json_encode($options);
        }

        return $options;
    }

    /**
     * Renders captcha HTML (if required)
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getCaptchaModel()->isRequired()) {
            return parent::_toHtml();
        }
        return '';
    }
}
