<?php
/*
* Copyright © 2016 SNM-Portal.com. All rights reserved.
* See LICENSE.txt for license details.
*/
namespace Snmportal\SyntaxHighlighter\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Head
 * @package Snmportal\SyntaxHighlighter\Block
 */
class Head extends Template
{
    const CFRONTEND = 'snmportal_syntaxhighlighter/general/frontend';
    const HI = 'snmportal_syntaxhighlighter/general/frontend_highlighter';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_scopeConfig->isSetFlag(self::CFRONTEND, ScopeInterface::SCOPE_STORE) &&
            $this->_scopeConfig->getValue(self::HI, ScopeInterface::SCOPE_STORE) == 'codemirror';

    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        if ($this->isEnabled()) {
            $this->pageConfig->addPageAsset('Snmportal_SyntaxHighlighter/cm/lib/codemirror.css');
            $this->pageConfig->addPageAsset('Snmportal_SyntaxHighlighter/cm/lib/snm.css');
            $this->pageConfig->addPageAsset('Snmportal_SyntaxHighlighter/js/snm_cm.js');
        }
        return parent::_prepareLayout();
    }
}