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
 
namespace Anowave\TaxSwitch\Plugin\Framework\App\PageCache;

use Anowave\TaxSwitch\Plugin\Framework\App\Plugin;

class Identifier
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
		/**
		 * Set helper
		 *
		 * @var \Anowave\TaxSwitch\Helper\Data $helper
		 */
		$this->helper = $helper;
	}
	
	/**
	 * Modify cache identifier based on tax display setting 
	 * 
	 * @param \Magento\Framework\App\PageCache\Identifier $identifier
	 * @param string $value
	 * @return string
	 */
	public function afterGetValue(\Magento\Framework\App\PageCache\Identifier $identifier, $value)
	{
		if ($this->helper->isActive())
		{
			$value .= $this->helper->getCurrentTaxDisplay();
		}
		
		return $value;
	}
}