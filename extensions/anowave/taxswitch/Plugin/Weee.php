<?php
/**
 * Anowave Magento 2 Tax Switcher
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_TaxSwitch
 * @copyright 	Copyright (c) 2020 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */
 
namespace Anowave\TaxSwitch\Plugin;

class Weee
{
    /**
     * @var \Anowave\TaxSwitch\Helper\Data
     */
    protected $helper;
    
    /**
     * Constructor 
     * 
     * @param \Anowave\TaxSwitch\Helper\Data $helper
     */
    public function __construct
    (
        \Anowave\TaxSwitch\Helper\Data $helper
    )
    {
        $this->helper = $helper;
    }
    
    /**
     * Modify display including tax price
     * 
     * @param \Magento\Weee\Block\Item\Price\Renderer $context
     * @param boolean $result
     */
    public function afterDisplayPriceInclTax(\Magento\Weee\Block\Item\Price\Renderer $context, $result)
    {
        if (!$this->helper->isActive()) 
        {
            return $result;
        }

        if (\Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX === $this->helper->getCurrentTaxDisplay())
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * Modify display excluding tax price
     *
     * @param \Magento\Weee\Block\Item\Price\Renderer $context
     * @param boolean $result
     */
    public function afterDisplayPriceExclTax(\Magento\Weee\Block\Item\Price\Renderer $context, $result)
    {
        if (!$this->helper->isActive()) 
        {
            return $result;
        }

        if (\Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX === $this->helper->getCurrentTaxDisplay())
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * Modify display both prices
     *
     * @param \Magento\Weee\Block\Item\Price\Renderer $context
     * @param boolean $result
     */
    public function displayBothPrices(\Magento\Weee\Block\Item\Price\Renderer $context, $result)
    {
        if (!$this->helper->isActive()) 
        {
            return $result;
        }
        
        if (\Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH === $this->helper->getCurrentTaxDisplay())
        {
            return true;
        }
        
        return false;
    }
}