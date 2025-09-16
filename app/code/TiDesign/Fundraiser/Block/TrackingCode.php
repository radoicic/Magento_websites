<?php

namespace TiDesign\Fundraiser\Block;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\View\Element\Template;
use TiDesign\Fundraiser\Helper\Config;

class TrackingCode extends Template
{
    /**
     * @param Template\Context $context
     * @param Config $configHelper
     * @param SessionManagerInterface $sessionManager
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        protected Config $configHelper,
        protected SessionManagerInterface $sessionManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return string|null
     */
    public function getTrackingCodeParam()
    {
        return $this->configHelper->getTrackingCodeParam();
    }
}
