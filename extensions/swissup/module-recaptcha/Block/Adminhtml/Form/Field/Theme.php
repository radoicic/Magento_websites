<?php

namespace Swissup\Recaptcha\Block\Adminhtml\Form\Field;

class Theme extends AbstractRenderer
{
    /**
     * @param \Magento\Framework\View\Element\Context       $context
     * @param \Swissup\Recaptcha\Model\Config\Captcha\Theme $source
     * @param array                                         $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Swissup\Recaptcha\Model\Config\Captcha\Theme $source,
        array $data = []
    ) {
        parent::__construct($context, $source, $data);
    }
}
