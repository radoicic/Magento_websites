<?php

namespace Swissup\Recaptcha\Block\Adminhtml\Form\Field;

class Rtype extends AbstractRenderer
{
    /**
     * @param \Magento\Framework\View\Element\Context       $context
     * @param \Swissup\Recaptcha\Model\Config\Captcha\Rtype $source
     * @param array                                         $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Swissup\Recaptcha\Model\Config\Captcha\Rtype $source,
        array $data = []
    ) {
        parent::__construct($context, $source, $data);
    }
}
