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

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;

class Debug extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface, HttpPostActionInterface, HttpGetActionInterface
{
	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $resultJsonFactory;
	
	/**
	 * @var \Magento\Framework\Session\SessionManagerInterface
	 */
	protected $session;

	/**
	 * @var \Anowave\TaxSwitch\Helper\Data
	 */
	protected $helper;
	
	/**
	 * 
	 * @var \Anowave\TaxSwitch\Model\Api
	 */
	private $api;
	
	/**
	 * Constructor
	 * 
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	 * @param \Magento\Framework\Session\SessionManagerInterface $session
	 * @param \Anowave\TaxSwitch\Helper\Data $helper
	 */
	public function __construct
	(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Magento\Framework\Session\SessionManagerInterface $session,
	    \Anowave\TaxSwitch\Helper\Data $helper
	)
	{
		parent::__construct($context);

		/**
		 * Set response type factory
		 *
		 * @var \Magento\Framework\Controller\Result\JsonFactory
		*/
		$this->resultJsonFactory = $resultJsonFactory;
		
		/**
		 * Set session 
		 * 
		 * @var \Magento\Framework\Session\SessionManagerInterface
		 */
		$this->session = $session;
		
		/**
		 * Set helper
		 * 
		 * @var \Anowave\TaxSwitch\Controller\Index\Debug $helper
		 */
		$this->helper = $helper;
	}
	/**
	 * Execute controller
	 *
	 * @see \Magento\Framework\App\ActionInterface::execute()
	 */
	public function execute()
	{
		$response = $this->resultJsonFactory->create();
		
		if ($this->helper->usePrecisionServices())
		{
		    $insights = $this->getApi()->getInsights();
		}
		else 
		{
		    $insights = [];
		}
		
		$data = [];
		
		$data[] = "Visitor IP: {$this->helper->getIp()}";
		
		if ($insights)
		{
		    $data[] = "MaxMind Detected Country: {$insights->country->iso_code}";
		    $data[] = "MaxMind Remaining Queries: {$insights->maxmind->queries_remaining}";
		}
		
		$response->setData($data);

		return $response;
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
	
	
	/**
	 * Create CSRF Exception
	 *
	 * {@inheritDoc}
	 * @see \Magento\Framework\App\CsrfAwareActionInterface::createCsrfValidationException()
	 */
	public function createCsrfValidationException(RequestInterface $request): ? InvalidRequestException
	{
	    return null;
	}
	
	/**
	 * Validate for CSRF
	 *
	 * {@inheritDoc}
	 * @see \Magento\Framework\App\CsrfAwareActionInterface::validateForCsrf()
	 */
	public function validateForCsrf(RequestInterface $request): ? bool
	{
	    return true;
	}
}