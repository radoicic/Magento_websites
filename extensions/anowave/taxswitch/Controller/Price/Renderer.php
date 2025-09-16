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
 
namespace Anowave\TaxSwitch\Controller\Price;

class Renderer extends \Magento\Framework\App\Action\Action
{
	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $resultJsonFactory;
	
	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $resultPageFactory;

	/**
	 * @var \Anowave\TaxSwitch\Helper\Data
	 */
	protected $helper;

	/**
	 * @var \Magento\Framework\View\Element\BlockFactory
	 */
	protected $blockFactory;
	
	/**
	 * @var Magento\Catalog\Model\ProductFactory 
	 */
	protected $productFactory;
	
	/**
	 * @var \Magento\Framework\Registry 
	 */
	protected $registry = null;

	/**
	 * Constgructor 
	 * 
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	 * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Anowave\TaxSwitch\Helper\Data $helper
	 * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
	 * @param \Magento\Catalog\Model\ProductFactory $productFactory
	 * @param \Magento\Framework\Registry $registry
	 */
	public function __construct
	(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Anowave\TaxSwitch\Helper\Data $helper,
		\Magento\Framework\View\Element\BlockFactory $blockFactory,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Framework\Registry $registry
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
		 * Set result page factory
		 * 
		 * @var \Magento\Framework\View\Result\PageFactory
		 */
		$this->resultPageFactory = $resultPageFactory;

		/**
		 * Set helper
		 *
		 * @var \Anowave\TaxSwitch\Helper\Data
		 */
		$this->helper = $helper;
		
		/**
		 * Set block factory 
		 * 
		 * @var \Magento\Framework\View\Element\BlockFactory
		 */
		$this->blockFactory = $blockFactory;
		
		/**
		 * Set product factory 
		 * 
		 * @var \Magento\Catalog\Model\ProductFactory
		 */
		$this->productFactory = $productFactory;
		
		/**
		 * Set registry 
		 * 
		 * @var \Magento\Framework\Registry
		 */
		$this->registry = $registry;
	}
	/**
	 * Execute controller
	 *
	 * @see \Magento\Framework\App\ActionInterface::execute()
	 */
	public function execute()
	{
		$data = [];
		
		$result = $this->resultPageFactory->create();
		
		/**
		 * Get renderer block 
		 * 
		 * @var unknown
		 */
		$block = $result->getLayout()->getBlock('product.price.render.default');
		
		if ($block)
		{
			foreach ((array) $this->getRequest()->getParam('id') as $id)
			{
				try 
				{
					$product = $this->productFactory->create()->load((int) $id);
					
					if ($product && $product->getId())
					{
						$data[] = 
						[
							'product_id' 			=> $product->getId(),
							'product_price_render' 	=> $block->render('final_price',$product,[])
						];
					}
				}
				catch (\Exception $e){}
			}
		}
		
		/**
		 * Create JSON response
		 * 
		 * @var \Magento\Framework\Controller\Result\Json
		 */
		$response = $this->resultJsonFactory->create();
		
		$response->setData($data);

		return $response;
	}
}