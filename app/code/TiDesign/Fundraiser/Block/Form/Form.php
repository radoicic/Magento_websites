<?php

namespace TiDesign\Fundraiser\Block\Form;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use TiDesign\Fundraiser\Helper\Data;

class Form extends Template
{
    private $_helper;
    public string $_htmlTemplate;

    public function __construct(
        Context $context,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_helper = $helper;
        $this->_htmlTemplate = "";
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @return bool
     */
    public function isLoggedin(): bool
    {
        return $this->_helper->isLoggedIn();
    }

    /**
     * @return string[]
     */
    public function getAllThemes()
    {
        return $this->_helper->getAllThemes();
    }

}


