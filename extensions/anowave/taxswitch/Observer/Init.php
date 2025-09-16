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
 
namespace Anowave\TaxSwitch\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class Init implements ObserverInterface
{
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
	 * @var \Magento\Framework\App\Cache\TypeListInterface
	 */
	protected $cacheTypeList = null;
	
	/**
	 * @var \Magento\Framework\App\Cache\Frontend\Pool
	 */
	protected $cacheFrontendPool = null;
	
	/**
	 * @var \Anowave\TaxSwitch\Model\CookieCountry
	 */
	protected $cookieCountry;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\View\Element\Context $context
	 * @param \Anowave\TaxSwitch\Helper\Data $helper
	 * @param \Magento\Tax\Model\Calculation\RateFactory $rates
	 * @param \Magento\Framework\App\State $state
	 * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
	 * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Framework\Registry $registry,
		\Magento\Framework\View\Element\Context $context,
		\Anowave\TaxSwitch\Helper\Data $helper,
		\Magento\Tax\Model\Calculation\RateFactory $rates,
		\Magento\Framework\App\State $state,
		\Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
		\Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
		\Anowave\TaxSwitch\Model\CookieCountry $cookieCountry,
		array $data = []
	)
	{
		$this->registry 			= $registry;
		$this->request 				= $context->getRequest();
		$this->session  			= $context->getSession();
		$this->helper				= $helper;
		$this->rates 				= $rates;
		$this->state				= $state;
		$this->cacheTypeList 		= $cacheTypeList;
		$this->cacheFrontendPool 	= $cacheFrontendPool;
		$this->cookieCountry 		= $cookieCountry;
	}
	
	/**
	 * Execute 
	 * 
	 * @param EventObserver $observer
	 */
	public function execute(EventObserver $observer)
	{
		/**
		 * Set tax display session
		 */
		if ($this->request->getParam(\Anowave\TaxSwitch\Helper\Data::CONTEXT_TAX_DISPLAY))
		{
			$this->session->setTaxDisplay((int) $this->request->getParam(\Anowave\TaxSwitch\Helper\Data::CONTEXT_TAX_DISPLAY));
		}
		
		/**
		 * Set tax display session by country
		 */
		if ($this->request->getParam(\Anowave\TaxSwitch\Helper\Data::CONTEXT_TAX_DISPLAY_COUNTRY))
		{
			$iso = $this->request->getParam(\Anowave\TaxSwitch\Helper\Data::CONTEXT_TAX_DISPLAY_COUNTRY);
			
			$this->session->setTaxDisplay
			(
				$this->helper->getCountryTaxDisplay
				(
					$iso
				)
			);
			
			/**
			 * Set session tax display
			 */
			$this->session->setTaxDisplayCountry($iso);
			
			/**
			 * Set tax display country cookie
			 */
			$this->cookieCountry->set($iso);
		}
	}
}
