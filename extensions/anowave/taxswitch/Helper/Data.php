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

namespace Anowave\TaxSwitch\Helper;

use Anowave\Package\Helper\Package;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\GroupFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Registry;

class Data extends Package
{
	/**
	 * Tax display $_GET key
	 * 
	 * @var string
	 */
	const CONTEXT_TAX_DISPLAY = 'tax_display';
	
	/**
	 * Tax display by countey $_GET key
	 * 
	 * @var string
	 */
	const CONTEXT_TAX_DISPLAY_COUNTRY = 'country';

	const CONTEXT_TAX_DISPLAY_COUNTRY_TEST = 'country_test';
	
	/**
	 * Package name
	 * 
	 * @var string
	 */
	protected $package = 'MAGE2-TAXSWITCH';
	
	/**
	 * Config path 
	 * 
	 * @var string
	 */
	protected $config = 'taxswitch/general/license';
	
	/**
	 * @var \Magento\Framework\App\Http\Context
	 */
	protected $httpContext = null;
	
	/**
	 * @var \Magento\Customer\Model\Session $customerSession
	 */
	protected $customerSession;
	
	/**
	 * @var \Magento\Customer\Api\CustomerRepositoryInterface
	 */
	protected $customerRepositoryInterface;
	
	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $registry;
	
	/**
	 * @var FilterBuilder
	 */
	protected $_filterBuilder;
	
	/**
	 * @var GroupRepositoryInterface
	 */
	protected $_groupRepository;
	
	/**
	 * @var SearchCriteriaBuilder
	 */
	protected $_searchCriteriaBuilder;
	
	/**
	 * @var GroupFactory
	 */
	protected $_groupFactory;
	
	/**
	 * @var \Anowave\TaxSwitch\Model\Cookie
	 */
	protected $cookie;
	
	/**
	 * @var Magento\Framework\App\Request\Http
	 */
	protected $request;
	
	/**
	 * @var \Magento\Tax\Model\Calculation\RateFactory
	 */
	protected $rates = null;
	
	/**
	 * @var \Anowave\TaxSwitch\Model\CookieCountry
	 */
	protected $cookieCountry;
	
	private $customer;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Customer\Model\Session $customerSession
	 * @param \Magento\Framework\App\Http\Context $httpContext
	 * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
	 * @param Registry $registry
	 * @param FilterBuilder $filterBuilder
	 * @param GroupRepositoryInterface $groupRepository
	 * @param SearchCriteriaBuilder $searchCriteriaBuilder
	 * @param GroupFactory $groupFactory
	 * @param \Anowave\TaxSwitch\Model\Cookie $cookie
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\App\Http\Context $httpContext,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
		Registry $registry,
		FilterBuilder $filterBuilder,
		GroupRepositoryInterface $groupRepository,
		SearchCriteriaBuilder $searchCriteriaBuilder,
		GroupFactory $groupFactory,
		\Anowave\TaxSwitch\Model\Cookie $cookie,
		\Magento\Tax\Model\Calculation\RateFactory $rates,
		\Anowave\TaxSwitch\Model\CookieCountry $cookieCountry,
		array $data = []
	)
	{
		parent::__construct($context);
		
		/**
		 * Set request 
		 * 
		 * @var \Magento\Framework\App\Request\Http $request
		 */
		$this->request = $context->getRequest();
		
		/**
		 * Set session
		 *
		 * @var \Magento\Customer\Model\Session $session
		 */
		$this->customerSession = $customerSession;
		
		/**
		 * Set http context 
		 * 
		 * @var \Anowave\TaxSwitch\Helper\Data $httpContext
		 */
		$this->httpContext = $httpContext;
		
		/**
		 * Set customer repository interface 
		 * 
		 * @var \Anowave\TaxSwitch\Helper\Data $customerRepositoryInterface
		 */
		$this->customerRepositoryInterface = $customerRepositoryInterface;
		
		/**
		 * Set registry 
		 * 
		 * @var \Anowave\TaxSwitch\Helper\Data $registry
		 */
		$this->registry = $registry;
		
		/**
		 * Set filter builder 
		 * 
		 * @var \Anowave\TaxSwitch\Helper\Data $_filterBuilder
		 */
		$this->_filterBuilder = $filterBuilder;
		
		/**
		 * Set group respository
		 *
		 * @var GroupRepositoryInterface $_groupRepository
		 */
		$this->_groupRepository = $groupRepository;
		
		/**
		 * Set search criteria builder
		 *
		 * @var SearchCriteriaBuilder $_searchCriteriaBuilder
		 */
		$this->_searchCriteriaBuilder = $searchCriteriaBuilder;
		
		/**
		 * Set group factory
		 *
		 * @var GroupFactory $_groupFactory
		 */
		$this->_groupFactory = $groupFactory;
		
		/**
		 * Set cookie
		 * 
		 * @var \Anowave\TaxSwitch\Model\Cookie $cookie
		 */
		$this->cookie = $cookie;
		
		/**
		 * Set cookie country
		 * 
		 * @var \Anowave\TaxSwitch\Model\CookieCountry $cookieCountry
		 */
		$this->cookieCountry = $cookieCountry;
		
		/**
		 * Set rates 
		 * 
		 * @var \Magento\Tax\Model\Calculation\RateFactory $rates
		 */
		$this->rates = $rates;
	}
	/**
	 * Check if module is active
	 * 
	 * @return boolean
	 */
	public function isActive()
	{
		return 1 === (int) @$this->getConfig('taxswitch/general/active');
	}
	
	/**
	 * Check if switcher dropdown is visible 
	 * 
	 * @return boolean
	 */
	public function isVisible()
	{
		return 1 === (int) @$this->getConfig('taxswitch/general/visible');
	}
	
	/**
	 * Check if debug mode
	 * 
	 * @return bool
	 */
	public function isDebugMode() : bool
	{
	    return 1 === (int) @$this->getConfig('taxswitch/maxmind/debug');
	}
	
	/**
	 * Enable Max Mind Precision services
	 * 
	 * @return bool
	 */
	public function usePrecisionServices() : bool
	{
		return 1 === (int) @$this->getConfig('taxswitch/maxmind/active');
	}
	
	/**
	 * Render switcher as KO component 
	 * 
	 * @return bool
	 */
	public function usePrivateContent() : bool
	{
		return 1 === (int) @$this->getConfig('taxswitch/advanced/private_content');
	}
	
	/**
	 * Add including option
	 * 
	 * @return bool
	 */
	public function addInclOption() : bool
	{
	    return 1 === (int) @$this->getConfig('taxswitch/advanced/add_incl_option');
	}
	
	/**
	 * Add excluding option
	 *
	 * @return bool
	 */
	public function addExclOption() : bool
	{
	    return 1 === (int) @$this->getConfig('taxswitch/advanced/add_excl_option');
	}
	
	/**
	 * Add both option
	 *
	 * @return bool
	 */
	public function addBothOption() : bool
	{
	    return 1 === (int) @$this->getConfig('taxswitch/advanced/add_both_option');
	}
	
	/**
	 * Get toggle background color
	 * 
	 * @return string
	 */
	public function getToggleBackground()
	{
	    return @$this->getConfig('taxswitch/general/toggle_background');
	}
	
	/**
	 * Get display template 
	 * 
	 * @return number
	 */
	public function getDisplayTemplate()
	{
	    $template = (int) $this->getConfig('taxswitch/general/template');
	    
	    if (!in_array($template, [\Anowave\TaxSwitch\Model\System\Config\Source\Template::AS_SELECT, \Anowave\TaxSwitch\Model\System\Config\Source\Template::AS_RADIOS, \Anowave\TaxSwitch\Model\System\Config\Source\Template::AS_TOGGLE]))
	    {
	        $template = \Anowave\TaxSwitch\Model\System\Config\Source\Template::AS_SELECT;
	    }

	    return $template;
	}
	
	/**
	 * Get show prices text 
	 * 
	 * @return string
	 */
	public function getShowPricesText() : string
	{
	    return (string) $this->getConfig('taxswitch/advanced/show_prices_text');
	}
	
	/**
	 * Check if customer is logged 
	 * 
	 * @return bool
	 */
	public function isLogged() : bool
	{
		if ($this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH))
		{
			return true;
		}
		else if($this->customerSession->isLoggedIn())
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Get customer
	 */
	public function getCustomer()
	{
		if (!$this->customer)
		{
			if ($this->registry->registry(\Anowave\TaxSwitch\Plugin\Depersonalize::DEPERSONALIZE_KEY) > 0)
			{
				$this->customer = $this->customerRepositoryInterface->getById($this->registry->registry(\Anowave\TaxSwitch\Plugin\Depersonalize::DEPERSONALIZE_KEY));
			}
			else if ($this->customerSession->getCustomerId())
			{
				$this->customer = $this->customerRepositoryInterface->getById($this->customerSession->getCustomerId());
			}
		}
		
		return $this->customer;
	}
	
	/**
	 * Get group tax display 
	 * 
	 * @return NULL
	 */
	public function getCurrentCustomerGroupTaxDisplay()
	{
		if ($this->isLogged())
		{
			$group_id = (int) $this->getCustomer()->getGroupId();
		}
		else 
		{
			$_filter =
			[
				$this->_filterBuilder->setField('customer_group_code')->setConditionType('eq')->setValue('NOT LOGGED IN')->create()
			];
			
			/**
			 * Load all groups by code
			 *
			 * @var array $customerGroups
			 */
			$customerGroups = $this->_groupRepository->getList($this->_searchCriteriaBuilder->addFilters($_filter)->create())->getItems();
			
			/**
			 * Get first group
			 *
			 * @var Ambiguous $customerGroup
			 */
			$customerGroup = array_shift($customerGroups);
			
			if($customerGroup)
			{
				$group_id = $customerGroup->getId();
			}
		}
		
		try 
		{
			$group = $this->_groupFactory->create();
			
			/**
			 * Load group by id
			 */
			$group->load($group_id);
			
			$group_tax_display = (int) $group->getData(\Anowave\TaxSwitch\Plugin\Group\Form::FIELD);
			
			if ($group_tax_display)
			{
				return $group_tax_display;
			}
			
		}
		catch (\Exception $e){}
		
		return null;
	}
	
	/**
	 * Get current tax display 
	 * 
	 * @return number|mixed|NULL
	 */
	public function getCurrentTaxDisplay()
	{
		$tax_display = null;
		
		if (null === $tax_display = $this->registry->registry(static::CONTEXT_TAX_DISPLAY))
		{
			/**
			 * Load tax display from request
			 */
			if ($this->request->getParam(static::CONTEXT_TAX_DISPLAY))
			{
				$tax_display = (int) $this->request->getParam(static::CONTEXT_TAX_DISPLAY);
				
				return $tax_display;
			}
			
			/**
			 * Load tax display by country from request
			 */
			if ($this->request->getParam(static::CONTEXT_TAX_DISPLAY_COUNTRY))
			{
				$iso = $this->request->getParam(static::CONTEXT_TAX_DISPLAY_COUNTRY);
				
				/**
				 * Get tax display by country
				 */
				$tax_display = (int) $this->getCountryTaxDisplay($iso);
				
				/**
				 * Set session variable
				 */
				$this->customerSession->setTaxDisplayCountry($iso);
				
				/**
				 * Set cookie
				 */
				$this->cookieCountry->set($iso);
				
				return $tax_display;
			}
			
			/**
			 * Load tax display from cookie
			 */
			$tax_display = $this->cookie->get();
			
			if (!$tax_display)
			{
				/**
				 * Tax display by group
				 */
				if (null !== $group_tax_display = $this->getCurrentCustomerGroupTaxDisplay())
				{
					$tax_display = $group_tax_display;
				}
			}
		}
		
		return $tax_display;
	}
	
	/**
	 * Get tax display by country ISO code
	 * 
	 * @param string $country
	 * @return string
	 */
	public function getCountryTaxDisplay($country = '')
	{
		foreach ($this->rates->create()->getCollection()->addFieldToFilter('tax_country_id', $country) as $rate)
		{
			if ((float) $rate->getRate() > 0)
			{	
				return \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX;
			}
		}
		
		return \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX;
	}
	
	/**
	 * Get default tax display 
	 * 
	 * @return number
	 */
	public function getDefaultTaxDisplay()
	{
		return (int) $this->getConfig(\Magento\Tax\Model\Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE);
	}
	
	public function getTrack()
	{
		return json_encode
		(
			[
				'enable' 		=> 1 === (int) $this->getConfig('taxswitch/analytics/track'),
				'type'	 		=> $this->getConfig('taxswitch/analytics/type'),
				'fieldsObject' 	=> 
				[
					'hitType' 		=> 'event',
					'eventCategory' => __('Tax Switcher'),
					'eventAction' 	=> 'switch',
					'eventLabel' 	=> ''
				],
				'fieldsObjectDatalayer' =>
				[
					'hitType' 		=> 'event',
					'eventCategory' => __('Tax Switcher'),
					'eventAction' 	=> 'switch',
					'eventLabel' 	=> ''
				]
			]
		);
	}
	
	public function getCookie()
	{
		return $this->cookie;
	}

	public function getRequest()
	{
		return $this->request;
	}

	public function getRates()
	{
		return $this->rates;
	}
	
	
	/**
	 * Get IP
	 *
	 * @return string
	 */
	public function getIp() : string
	{
	    if (isset($_SERVER['HTTP_CF_CONNECTING_IP']))
	    {
	        return $_SERVER['HTTP_CF_CONNECTING_IP'];
	    }
	    
	    return $_SERVER['REMOTE_ADDR'];
	}
}
