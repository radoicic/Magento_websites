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

use Magento\Framework\View\Element\Template;

class Switcher extends \Magento\Framework\View\Element\Template
{
	/**
	 * @var \Anowave\TaxSwitch\Helper\Data
	 */
	protected $helper;
	
	/**
	 * @var \Magento\Framework\Data\Form\FormKey
	 */
	protected $formKey;
	
	/**
	 * @var \Magento\Framework\Session\SessionManagerInterface
	 */
	protected $session;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Anowave\TaxSwitch\Helper\Data $helper
	 * @param \Magento\Framework\Data\Form\FormKey $formKey
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Framework\View\Element\Template\Context $context,
		\Anowave\TaxSwitch\Helper\Data $helper,
		\Magento\Framework\Data\Form\FormKey $formKey,
	    \Magento\Framework\Session\SessionManagerInterface $session,
		array $data = []
	)
	{
		parent::__construct($context, $data);
		
		$this->helper = $helper;
		
		/**
		 * Set form key 
		 * 
		 * @var \Magento\Framework\Data\Form\FormKey $formKey
		 */
		$this->formKey = $formKey;
		
		/**
		 * Set session
		 *
		 * @var \Magento\Framework\Session\SessionManagerInterface
		 */
		$this->session = $session;
	}
	
	/**
	 * Get identities
	 *
	 * @return array
	 */
	public function getIdentities()
	{
		return [\Anowave\TaxSwitch\Helper\Data::CONTEXT_TAX_DISPLAY];
	}
	
	/**
	 * Get block cache life time
	 *
	 * @return int|null
	 */
	protected function getCacheLifetime()
	{
		return null;
	}
	
	/**
	 * Set options
	 */
	public function getOptions()
	{
		$current = ($this->_request->getParam('tax_display') ? $this->_request->getParam('tax_display') : $this->_session->getTaxDisplay());
		
		if (!$current)
		{
			$current = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Tax\Model\Config')->getPriceDisplayType();
		}
		
		/**
		 * Dropdown options
		 * 
		 * @var array $options
		 */
		$options = [];
		
		if ($this->helper->addInclOption())
		{
		    $options['inc'] = 
		    [
		        'value' 	=> \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX,
		        'label' 	=> __('Incl. VAT'),
		        'checked'	=> $current == \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX ? 1 : 0
		    ];
		}
		
		if ($this->helper->addExclOption())
		{
		    $options['exc'] = 
		    [
		        'value' 	=> \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX,
		        'label' 	=> __('Excl. VAT'),
		        'checked' 	=> $current == \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX ? 1 : 0
		    ];
		}
		
		if ($this->helper->addBothOption())
		{
		    $options['both'] = 
		    [
		        'value' 	=> \Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH,
		        'label' 	=> __('Both'),
		        'checked' 	=> $current == \Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH ? 1 : 0
		    ];
		}
		
		return $options;
	}
	
	public function getFormKey()
	{
		return $this->formKey->getFormKey();
	}
	
	/**
	 * Invalidate mini-cart
	 * 
	 * @return bool
	 */
	public function isInvalidate() : bool
	{
	    $invalidate = $this->session->getInvalidate();
	    
	    if ($invalidate)
	    {
	        $this->session->unsetData('invalidate');
	        
	        return true;
	    }
	    
	    return false;
	}
	
	/**
	 * Get helper
	 * 
	 * @return \Anowave\TaxSwitch\Helper\Data
	 */
	public function getHelper()
	{
		return $this->helper;
	}
	
	public function _toHtml()
	{
		if (!$this->helper->isActive() || !$this->helper->isVisible())
		{
			return '';	
		}
		
		return $this->helper->filter(parent::_toHtml());
	}
}