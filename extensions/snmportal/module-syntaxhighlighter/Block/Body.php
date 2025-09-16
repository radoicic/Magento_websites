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
class Body extends Template
{
    const CFRONTEND = 'snmportal_syntaxhighlighter/general/frontend';
    const HI = 'snmportal_syntaxhighlighter/general/frontend_highlighter';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_scopeConfig->isSetFlag(self::CFRONTEND, ScopeInterface::SCOPE_STORE) &&
            ($this->_scopeConfig->getValue(self::HI, ScopeInterface::SCOPE_STORE) == 'highlightjs');
    }

    protected function _afterToHtml($html)
    {
        if ($this->isEnabled()) {
            $assetRepo = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Framework\View\Asset\Repository');
            $asset = $assetRepo->createAsset('Snmportal_SyntaxHighlighter/hl/styles/tomorrow-night-bright.css');
            $urlCss = $asset->getUrl();
            $html.='<script > 
                require([    \'ko\'], function (ko) {
                    ko.bindingHandlers.snm_syntaxhighlighter = {
                        init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
                            require([    \'Snmportal_SyntaxHighlighter/js/snm_cmhl\'], function (cm) {
                                var mode = ko.unwrap(valueAccessor() || \'magento\');
                                cm.initElement(element,mode,"'.$urlCss.'");
                            });
                        }
                    }
                });
        </script>';
        }
        return $html;
    }

}