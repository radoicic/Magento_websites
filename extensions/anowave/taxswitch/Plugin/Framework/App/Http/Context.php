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
 
namespace Anowave\TaxSwitch\Plugin\Framework\App\Http;

use Anowave\TaxSwitch\Plugin\Framework\App\Plugin;
use Magento\Framework\App\Http\Context as HttpContext;

class Context
{	
	const KEY = 'tax_display';
	
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
	 * Add customer_id to vary string data to ensure private content
	 * 
	 * @param HttpContext $subject
	 * @return array
	 */
    public function beforeGetVaryString(HttpContext $subject)
    {
    	if ($this->helper->isActive())
    	{
    		$subject->setValue(static::KEY, $this->helper->getCurrentTaxDisplay(), 0);
    	}
    	
    	return [];
    }
}