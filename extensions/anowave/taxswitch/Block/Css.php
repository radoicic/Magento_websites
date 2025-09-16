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
 
namespace Anowave\TaxSwitch\Block;

use Magento\Backend\Block\AbstractBlock;

class Css extends \Magento\Backend\Block\AbstractBlock
{
	/**
	 * @var \Anowave\TaxSwitch\Helper\Data
	 */
	protected $helper;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Backend\Block\Context $context
	 * @param \Anowave\TaxSwitch\Helper\Data $helper
	 */
	public function __construct
	(
		\Magento\Backend\Block\Context $context,
		\Anowave\TaxSwitch\Helper\Data $helper, 
		array $data = []
	)
	{	
		$this->helper = $helper;
		
		parent::__construct($context);
	}
	
	/**
	 * @override
	 * @see \Magento\Backend\Block\AbstractBlock::_construct()
	 * @return void
	 */
	protected function _construct() 
	{
		/**
		 * @todo Implement AJAX based price loading
		 */
		if (true)
		{
			\Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\View\Page\Config')->addPageAsset('Anowave_TaxSwitch::css/tax.css');
		}
	}
	
	public function getHelper()
	{
		return $this->helper;
	}
}