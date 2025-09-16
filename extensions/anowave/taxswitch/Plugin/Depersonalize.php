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

use Magento\PageCache\Model\DepersonalizeChecker;
use Magento\Framework\Registry;

/**
 * Google Analytics module observer
 *
 */
class Depersonalize
{
	const DEPERSONALIZE_KEY = 'cache_session_tax_customer_id';
	
	/**
	 * @var DepersonalizeChecker
	 */
	protected $depersonalizeChecker;
	
	/**
	 * @var \Magento\Framework\Session\SessionManagerInterface
	 */
	protected $session;
	
	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $registry;
	
	/**
	 * @var \Magento\Customer\Model\Session
	 */
	protected $customerSession;

	/**
	 * Constructor
	 * 
	 * @param DepersonalizeChecker $depersonalizeChecker
	 * @param \Magento\Framework\Session\SessionManagerInterface $session
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Customer\Model\Session $customerSession
	 */
	public function __construct
	(
		DepersonalizeChecker $depersonalizeChecker,
		\Magento\Framework\Session\SessionManagerInterface $session,
		\Magento\Framework\Registry $registry,
		\Magento\Customer\Model\Session $customerSession
	) 
	{
		/**
		 * Set DepersonalizeChecker
		 * 
		 * @var DepersonalizeChecker $depersonalizeChecker
		 */
		$this->depersonalizeChecker = $depersonalizeChecker;
		
		/**
		 * Set session interface
		 * 
		 * @var \Magento\Framework\Session\SessionManagerInterface $session
		 */
		$this->session 	= $session;
		
		/**
		 * Set registry 
		 * 
		 * @var \Magento\Framework\Registry $registry
		 */
		$this->registry	= $registry;
		
		/**
		 * Set customer session
		 *
		 * @var \Magento\Customer\Model\Session $customerSession
		 */
		$this->customerSession = $customerSession;
	}
	
	/**
	 * After generate Xml
	 *
	 * @param \Magento\Framework\View\LayoutInterface $subject
	 * @param \Magento\Framework\View\LayoutInterface $result
	 * @return \Magento\Framework\View\LayoutInterface
	 */
	public function afterGenerateXml(\Magento\Framework\View\LayoutInterface $subject, $result)
	{
		/**
		 * Depersonalize tax display
		 */
		if (is_null($this->registry->registry('tax_display')) && 0 < (int) $this->session->getTaxDisplay())
		{
			$this->registry->register('tax_display', (int) $this->session->getTaxDisplay());
		}
		
		/**
		 * Depersonalize customer
		 */
		if (is_null($this->registry->registry(static::DEPERSONALIZE_KEY)) && 0 < (int) $this->customerSession->getCustomerId())
		{
			$this->registry->register(static::DEPERSONALIZE_KEY, (int) $this->customerSession->getCustomerId());
		}
		
		return $result;
	}
}