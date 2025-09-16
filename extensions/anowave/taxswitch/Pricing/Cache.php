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

class Cache extends \Magento\Framework\Cache\Frontend\Decorator\TagScope
{
	/**
	 * Set to true to disable HTML output caching for blocks
	 * 
	 * @var bool
	 */
	const PRIVATE_CONTENT = true; 
	
	/**
	 * Load cache
	 * 
	 * @see \Magento\Framework\Cache\Frontend\Decorator\Bare::load()
	 * @see 
	 * 
	 * This is a dirty way to STOP Magento from caching block's output. We are happy to consider any suggestions to show different price display to different
	 * public visitors. 
	 * 
	 */
	public function load($identifier)
	{
		if (self::PRIVATE_CONTENT && 0 === strpos($identifier, 'BLOCK_'))
		{
			return false;
		}
		
		return parent::load($identifier);
	}
}