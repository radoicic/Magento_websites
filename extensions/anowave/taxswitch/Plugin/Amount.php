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

class Amount
{
	/**
	 * @var \Magento\Tax\Model\Config
	 */
	protected $taxConfig;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Tax\Model\Config $taxConfig
	 */
	public function __construct
	(
		\Magento\Tax\Model\Config $taxConfig
	)
	{
		$this->taxConfig = $taxConfig;
	}
	
	/**
	 * Amount plugin 
	 * 
	 * @param \Magento\Framework\Pricing\Render\Amount $amount
	 */
	public function beforeToHtml(\Magento\Framework\Pricing\Render\Amount $amount)
	{
		if (is_null($amount->getPriceDisplayLabel()))
		{
			switch($this->taxConfig->getPriceDisplayType())
			{
				case \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX: 
					$amount->setPriceDisplayLabel(__('Incl. tax'));
					break;
				case \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX:
					$amount->setPriceDisplayLabel(__('Excl. tax'));
					break;
				case \Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH:
					$amount->setPriceDisplayLabel(__('Both'));
					break;
			}
		}
	}
}