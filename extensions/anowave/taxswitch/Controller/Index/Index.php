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

class Index extends \Magento\Framework\App\Action\Action
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
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	 * @param \Magento\Framework\Session\SessionManagerInterface $session
	 */
	public function __construct
	(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Magento\Framework\Session\SessionManagerInterface $session
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
	}
	/**
	 * Execute controller
	 *
	 * @see \Magento\Framework\App\ActionInterface::execute()
	 */
	public function execute()
	{
		$response = $this->resultJsonFactory->create();
		
		$current = ($this->getRequest()->getParam('tax_display') ? $this->getRequest()->getParam('tax_display') : $this->session->getTaxDisplay());
		
		if (!$current)
		{
			$current = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Tax\Model\Config')->getPriceDisplayType();
		}
		
		$response->setData(
		[
			'switcher' => 
			[
				'label'  	=> __('Show prices'),
				'options' 	=> $this->getOptions(),
				'current'	=> $current
			]
		]);

		return $response;
	}
	
	public function getOptions()
	{
		return
		[
			[
				'value' 	=> (string) \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX,
				'label' 	=> __('Incl. VAT')
			],
			[
				'value' 	=> (string) \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX,
				'label' 	=> __('Excl. VAT')
			],
			[
				'value' 	=> (string) \Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH,
				'label' 	=> __('Both')
			]
		];
	}
}