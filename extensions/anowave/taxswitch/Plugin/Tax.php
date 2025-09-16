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

use Magento\Framework\Registry;

class Tax
{
	/**
	 * @var \Anowave\TaxSwitch\Model\Api
	 */
	protected $api = null;
	
	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $registry = null;
	
	/** 
	 * @var \Magento\Framework\App\RequestInterface
	 */
	protected $request = null;
	
	/**
	 * @var \Magento\Catalog\Model\Session
	 */
	protected $session = null;
	
	/**
	 * @var \Anowave\TaxSwitch\Helper\Data
	 */
	protected $helper = null;
	
	/**
	 * @var \Magento\Tax\Model\Calculation\RateFactory
	 */
	protected $rates = null;
	
	/**
	 * @var \Magento\Framework\App\State
	 */
	protected $state = null;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\View\Element\Context $context
	 * @param \Anowave\TaxSwitch\Helper\Data $helper
	 * @param \Magento\Tax\Model\Calculation\RateFactory $rates
	 * @param \Magento\Framework\App\State $state
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Framework\Registry $registry,
		\Magento\Framework\View\Element\Context $context,
		\Anowave\TaxSwitch\Helper\Data $helper,
		\Magento\Tax\Model\Calculation\RateFactory $rates,
		\Magento\Framework\App\State $state,
		array $data = []
	)
	{
		$this->registry 			= $registry;
		$this->request 				= $context->getRequest();
		$this->session  			= $context->getSession();
		$this->helper				= $helper;
		$this->rates 				= $rates;
		$this->state				= $state;
	}
	
	/**
	 * Dynamic tax display
	 *
	 * @param \Magento\Tax\Model\Config $config
	 * @param unknown $type
	 */
	public function afterGetPriceDisplayType(\Magento\Tax\Model\Config $config, $type)
	{
		if ($this->state->getAreaCode() === \Magento\Framework\App\Area::AREA_ADMINHTML)
		{
			return $type;
		}

		/**
		 * Get tax display 
		 * 
		 * @var int $tax_display
		 */
		$tax_display = $this->helper->getCurrentTaxDisplay();
		
		if ($tax_display)
		{
			$this->session->setTaxDisplay($tax_display);
			
			return $tax_display;
		}
		else
		{
			if ($this->helper->usePrecisionServices())
			{
				if (!($insights = $this->session->getInsights()))
				{
					$insights = $this->getApi()->getInsights();
					
					/**
					 * Set insights
					 */
					$this->session->setInsights($insights);
				}

				if ($insights)
				{
					if (property_exists($insights, 'registered_country'))
					{
						/**
						 * Get country name
						 *
						 * @var string
						 */
						$country = $insights->registered_country->names->en;
						
						/**
						 * Get country ISO code
						 *
						 * @var string
						 */
						$iso = $insights->registered_country->iso_code;
						
						/**
						 * Set country ISO code in session
						 */
						$this->session->setTaxDisplayCountry($iso);
						
						$rates = [];
						
						foreach ($this->rates->create()->getCollection()->addFieldToFilter('tax_country_id', $iso) as $rate)
						{
							if ((float) $rate->getRate() > 0)
							{
								$this->session->setTaxDisplay(\Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX);
								
								return \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX;
								
							}
						}
						
						$this->session->setTaxDisplay(\Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX);
						
						/**
						 * Display including tax for all countries without tax
						 */
						return \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX;
					}
				}
			}
		}
	
		return $type;
	}
	
	/**
	 * Get cart display incl. tax
	 *
	 * @param \Magento\Tax\Model\Config $config
	 * @param boolean $condition
	 * @return boolean
	 */
	public function afterDisplayCartPricesInclTax(\Magento\Tax\Model\Config $config, $condition)
	{
		if ($this->session->getTaxDisplay() === \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX)
		{
			$condition = true;
		}

		return $condition;
	}
	
	/**
	 * Get cart display excl. tax
	 *
	 * @param \Magento\Tax\Model\Config $config
	 * @param boolean $condition
	 * @return boolean
	 */
	public function afterDisplayCartPricesExclTax(\Magento\Tax\Model\Config $config, $condition)
	{
		if ($this->session->getTaxDisplay() === \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX)
		{
			$condition = true;
		}
		
		return $condition;
	}
	
	/**
	 * Get cart display both 
	 * 
	 * @param \Magento\Tax\Model\Config $config
	 * @param boolean $condition
	 * @return boolean
	 */
	public function afterDisplayCartPricesBoth(\Magento\Tax\Model\Config $config, $condition)
	{
		if ($this->session->getTaxDisplay() !== \Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH)
		{
			$condition = false;
		}

		return $condition;
	}
	
	public function afterDisplayCartSubtotalInclTax(\Magento\Tax\Model\Config $config, $condition)
	{
		if ($this->session->getTaxDisplay() === \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX)
		{
			$condition = true;
		}
		
		return $condition;
	}
	
	public function afterDisplayCartSubtotalExclTax(\Magento\Tax\Model\Config $config, $condition)
	{
		if ($this->session->getTaxDisplay() === \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX)
		{
			$condition = true;
		}
		
		return $condition;
	}

	public function afterDisplayCartSubtotalBoth(\Magento\Tax\Model\Config $config, $condition)
	{
		if ($this->session->getTaxDisplay() === \Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH)
		{
			$condition = true;
		}
		
		return $condition;
	}
	
	/**
	 * Get API
	 */
	protected function getApi()
	{
		if (!$this->api)
		{
			$this->api = new \Anowave\TaxSwitch\Model\Api($this->helper);
		}

		return $this->api;
	}
}