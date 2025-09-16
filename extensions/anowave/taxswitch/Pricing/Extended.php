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
 
namespace Anowave\TaxSwitch\Pricing;

class Extended extends \Magento\Catalog\Pricing\Render\FinalPriceBox
{
	/**
	 * Prevent block from caching
	 * 
	 * @see \Magento\Framework\Pricing\Render\PriceBox::getCacheLifetime()
	 */
	public function getCacheLifetime()
	{
		return null;
	}
}