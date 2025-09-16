<?php

namespace Swissup\Recaptcha\Block\Adminhtml\Form\Field;

class Badge extends AbstractRenderer
{
    /**
     * @param \Magento\Framework\View\Element\Context       $context
     * @param \Swissup\Recaptcha\Model\Config\Captcha\Badge $source
     * @param array                                         $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Swissup\Recaptcha\Model\Config\Captcha\Badge $source,
        array $data = []
    ) {
        parent::__construct($context, $source, $data);
    }

    public function getExtraParams()
    {
        return 'data-recaptcha-type="invisible"';
    }
}
