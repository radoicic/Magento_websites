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
 
namespace Anowave\TaxSwitch\Block\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template;

class Display extends \Magento\Config\Block\System\Config\Form\Field
{
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param array $data
	 */
    public function __construct
    (
    	\Magento\Backend\Block\Template\Context $context,
    	array $data = [])
    {
    	parent::__construct($context, $data);
    }
	
	/**
	 * Get element content
	 * 
	 * @see \Magento\Config\Block\System\Config\Form\Field::_getElementHtml()
	 */
	protected function _getElementHtml(AbstractElement $element)
	{
		return $this->getLayout()->createBlock('Anowave\TaxSwitch\Block\Comment')->setTemplate('display.phtml')->toHtml();
	}
}