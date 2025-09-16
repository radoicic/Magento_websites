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
 
namespace Anowave\TaxSwitch\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Text extends Template implements BlockInterface
{
	/**
	 * Set template 
	 * 
	 * @var string
	 */
	protected $_template = 'widget/display.phtml';
	
	/**
	 * @var \Anowave\TaxSwitch\Helper\Data
	 */
	protected $helper;
	
	/**
	 * @var \Magento\Tax\Model\Config
	 */
	protected $taxConfig;
	
	/**
	 * Constructor
	 * 
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Anowave\TaxSwitch\Helper\Data $helper
	 * @param \Magento\Tax\Model\Config $taxConfig
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Framework\View\Element\Template\Context $context,
		\Anowave\TaxSwitch\Helper\Data $helper,
		\Magento\Tax\Model\Config $taxConfig,
		array $data = []
	)
	{
		parent::__construct($context, $data);
		
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\TaxSwitch\Block\Widget\Display $helper
		 */
		$this->helper = $helper;
		
		/**
		 * Set tax config 
		 * 
		 * @var \Anowave\TaxSwitch\Block\Widget\Display $taxConfig
		 */
		$this->taxConfig = $taxConfig;
	}
	
	/**
	 * Get tax display 
	 * 
	 * @return \Magento\Framework\Phrase
	 */
	public function getText()
	{
		switch($this->taxConfig->getPriceDisplayType())
		{
		    case \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX: return $this->getData('text_incl_tax');
			case \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX: return $this->getData('text_excl_tax');
			case \Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH:		    return $this->getData('text_both_tax');
		}
		
		return '';
	}
}