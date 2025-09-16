<?php
	namespace TiDesign\Consignment\Controller\Ajax;
	
	use Magento\Framework\App\Action\Action;
	use Magento\Framework\App\ResponseInterface;
	use Magento\Framework\Controller\ResultFactory;
	use Magento\Framework\App\Config\ScopeConfigInterface;
	
	
	class Index extends \Magento\Framework\App\Action\Action
	{
		protected $resultPageFactory;
		protected $consignmentHelper;
		protected $sourceHelper;
		private $productRepository; 
		
		public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\TiDesign\Consignment\Helper\Consignment $consignmentHelper,
			\TiDesign\Consignment\Helper\InventorySource $sourceHelper,
			\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
			\Magento\Catalog\Api\ProductRepositoryInterface $productRepository
		) {
			parent::__construct($context);
			$this->consignmentHelper 		= $consignmentHelper;
			$this->sourceHelper 			= $sourceHelper;
			$this->resultJsonFactory 		= $resultJsonFactory;
			$this->productRepository 		= $productRepository;
		}
		public function execute() {
			$sku 		= $this->_request->getParam('sku');
			$source 	= $this->_request->getParam('source');
			$product 	= $this->productRepository->get($sku);
			$qty		= $this->consignmentHelper->getConsStock($product, $source);
			$data		= Array('qty'  => $qty);		
			
			$result = $this->resultJsonFactory->create();
			$result->setData($data);
			return $result;
		}

		
	}