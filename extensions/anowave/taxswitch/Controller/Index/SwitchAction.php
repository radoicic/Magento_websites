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
 
namespace Anowave\TaxSwitch\Controller\Index;

class SwitchAction extends \Magento\Framework\App\Action\Action
{
	/**
	 * @var \Magento\Framework\App\Http\Context
	 */
	protected $httpContext;
	
	/**
	 * @var \Magento\Framework\Session\SessionManagerInterface
	 */
	protected $session;
	
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;
	
	/**
	 * @var \Anowave\TaxSwitch\Model\Cookie
	 */
	protected $cookie;
	
	
	/**
	 * @var \Magento\Framework\App\Cache\TypeListInterface
	 */
	protected $cacheTypeList = null;
	
	/**
	 * @var \Magento\Framework\App\Cache\Frontend\Pool
	 */
	protected $cacheFrontendPool = null;

	/**
	 * @var \Magento\Framework\App\PageCache\Identifier
	 */
	protected $identifier;
	
	/**
	 * @var \Magento\Framework\App\PageCache\Cache
	 */
	protected $cache;
	
	/** 
	 * @var \Magento\PageCache\Model\Cache\Type
	 */
	protected $cacheType;
	
	/**
	 * @var \Anowave\TaxSwitch\Helper\Data
	 */
	protected $helper;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\Session\SessionManagerInterface $session
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\App\Http\Context $httpContext
	 * @param \Anowave\TaxSwitch\Model\Cookie $cookie
	 * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
	 * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
	 * @param \Magento\Framework\App\PageCache\Identifier $identifier
	 * @param \Magento\Framework\App\PageCache\Cache $cache
	 * @param \Anowave\TaxSwitch\Helper\Data $helper
	 */
	public function __construct
	(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Session\SessionManagerInterface $session,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\Http\Context $httpContext,
		\Anowave\TaxSwitch\Model\Cookie $cookie,
		\Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
		\Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
		\Magento\Framework\App\PageCache\Identifier $identifier,
		\Magento\Framework\App\PageCache\Cache $cache,
		\Anowave\TaxSwitch\Helper\Data $helper,
		\Magento\PageCache\Model\Cache\Type $cacheType
	)
	{
		parent::__construct($context);

		/**
		 * Set session 
		 * 
		 * @var \Magento\Framework\Session\SessionManagerInterface
		 */
		$this->session = $session;
		
		/**
		 * Set store manager
		 * 
		 * @var \Magento\Store\Model\StoreManagerInterface $storeManager
		 */
		$this->storeManager = $storeManager;
		
		/**
		 * Set http context 
		 * 
		 * @var \Anowave\TaxSwitch\Controller\Index\SwitchAction $httpContext
		 */
		$this->httpContext = $httpContext;
		
		/**
		 * Set cookie 
		 * 
		 * @var \Anowave\TaxSwitch\Model\Cookie $cookie
		 */
		$this->cookie = $cookie;
		
		/**
		 * Set cache type list 
		 * 
		 * @var \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
		 */
		$this->cacheTypeList = $cacheTypeList;
		
		/**
		 * Set cache frontend pool
		 * 
		 * @var \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
		 */
		$this->cacheFrontendPool = $cacheFrontendPool;
		
		/**
		 * Set identifier 
		 * 
		 * @var \Magento\Framework\App\PageCache\Identifier $identifier
		 */
		$this->identifier = $identifier;
		
		/**
		 * Set cache 
		 * 
		 * @var \Magento\Framework\App\PageCache\Cache $cache
		 */
		$this->cache = $cache;
		
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\TaxSwitch\Helper\Data $helper
		 */
		$this->helper = $helper;
		
		$this->cacheType = $cacheType;
	}
	
	/**
     * @return void
     */
    public function execute()
    {
    	$tax_display = null;
    	
        /**
         * Get tax display 
         * 
         * @var int $tax_display
         */
        $display = (int) $this->getRequest()->getParam(\Anowave\TaxSwitch\Helper\Data::CONTEXT_TAX_DISPLAY);
        
        switch($display)
        {
        	case \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX:
        	case \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX:
        	case \Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH:
        		$tax_display = $display;
        		break;
        }
        
        if ($tax_display) 
        {
        	/**
        	 * Set session
        	 */
           	$this->session->setTaxDisplay($tax_display);
           	
           	/**
           	 * Set cookie
           	 */
           	$this->cookie->set($tax_display);
           	
           	/**
           	 * Update HTTP context
           	 */
           	$this->httpContext->setValue(\Anowave\TaxSwitch\Helper\Data::CONTEXT_TAX_DISPLAY, $tax_display, $this->helper->getDefaultTaxDisplay());

           	$this->clearCache();
        }
        
        /**
         * Set invalidate
         */
        $this->session->setInvalidate(true);

        /**
         * 
         */
        $this->getResponse()->setNoCacheHeaders();
        $this->getResponse()->setRedirect
        (
        	$this->_redirect->getRedirectUrl($this->storeManager->getStore()->getBaseUrl())
        );
    }
    
    /**
     * Clear cache
     */
    protected function clearCache()
    { 
    	foreach ($this->cacheFrontendPool as $cacheFrontend)
    	{	
    		$cacheFrontend->getBackend()->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG,[$this->identifier->getValue()]);
    	}
    
    	return $this;
    }
}