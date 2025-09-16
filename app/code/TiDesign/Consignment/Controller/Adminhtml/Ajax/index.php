<?php
	namespace TiDesign\Consignment\Controller\Adminhtml\Ajax;
	
	use Magento\Framework\App\Action\Action;
	use Magento\Framework\App\ResponseInterface;
	use Magento\Framework\Controller\ResultFactory;
	use Magento\Framework\App\Config\ScopeConfigInterface;
	
	
	class Index extends \Magento\Backend\App\Action
	{
		protected $resultPageFactory;
		protected $consignmentHelper;
		protected $sourceHelper;
		
		protected $_customer;
		protected $_customerFactory;
		protected $_cusCol;
		protected 	$_websiteModel;

		
		public function __construct(
			\Magento\Backend\App\Action\Context $context,
			\TiDesign\Consignment\Helper\Consignment $consignmentHelper,
			\TiDesign\Consignment\Helper\InventorySource $sourceHelper,
			\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
            \Magento\Customer\Model\CustomerFactory $customerFactory,
            \Magento\Customer\Model\Customer $customers,
			\Magento\Customer\Model\ResourceModel\Grid\CollectionFactory $cusCol,
			\Magento\Store\Model\Website $websiteModel
		) {
			parent::__construct($context);
			$this->consignmentHelper 		= $consignmentHelper;
			$this->sourceHelper 			= $sourceHelper;
			$this->resultJsonFactory 		= $resultJsonFactory;
			$this->_customerFactory 		= $customerFactory;
			$this->_customer 				= $customers;
			$this->_cusCol 					= $cusCol;
			$this->_websiteModel 			= $websiteModel;
		}
		public function execute() {
			$data=[];
			$bak = $this->getRequest()->getParam('bak');		
			switch($bak){
				case "name":
					$name 		= $this->_request->getParam('name');
					$id 		= $this->_request->getParam('id');
					$nameCheck 	= $this->consignmentHelper->checkName($name,$id);
					if($nameCheck > 0){
						$data=Array('status'  => 0, 'message' => '<span id="name_check_message" class="admin__field-error" for="td_name">Name is already in use</span>');
					}else{
						$data=Array('status'  => 1, 'message' => '<span id="name_check_message"  class="admin__field-success">Name is usable</span>');
					}
				break;
				
				case "category":
					$name 			= $this->_request->getParam('category');
					$website		= $this->_request->getParam('website');
					
					$categoryCheck 	= $this->consignmentHelper->checkCategory($name,$website);
					if($categoryCheck){
						$data=Array('status'  => 1,  'message' => '<span id="category_check_message"  class="admin__field-success">Category name is usable</span>');
					}else{
						$data=Array('status'  => 0, 'message' => '<span id="category_check_message" class="admin__field-error" for="td_category">Category name or url key is already in use</span>');
					}
				break;
				
				case "source":
					$source			= $this->_request->getParam('source');
					$sourceCheck 	= $this->sourceHelper->checkSource($source);
					
					if(strpos($source, ' ') >0){
						$sourceCheck=true;
					}					
					if(!$sourceCheck){
						$data=Array('status'  => 1,  'message' => '<span id="source_check_message"  class="admin__field-success">Source name is usable</span>');
					}else{
						$data=Array('status'  => 0, 'message' => '<span id="source_check_message" class="admin__field-error" for="td_source">Source name is already in use</span>','data'  => json_encode($sourceCheck) );
					}
				break;
				
				case "customer":
					$txt			= $this->_request->getParam('customer_txt');
					$text			= "";

					//$result=$this->_cusCol->create()->addFieldToFilter("name",['like'=>('%' .$txt . '%')]);
					$result=$this->_cusCol->create()->addFullTextFilter($txt);
					if($result->getSize()>0){
						$text ='<div class="s-container"><div class="s-table">';
							foreach($result->getData() as $cData){
								$text .='<div class="c-cell">';
									$text .='<span class="inputc"><input type="radio" class="customer-selector" name="customer-selector" value="'.$cData['entity_id'].'"></span>';
									$text .='<span><span>'.$this->highlightKeywords($cData['name'], $txt).'</span>';
									$text .='<span>'.$this->highlightKeywords($cData['email'], $txt).'</span>';
									$text .='<span>'.$this->highlightKeywords($cData['billing_street'], $txt).'</span>';
									$text .='<span>'.$this->highlightKeywords($cData['billing_city'], $txt).', '.$this->highlightKeywords($cData['billing_postcode'], $txt).'</span>';
									$text .='<span>'.$this->highlightKeywords($cData['billing_region'], $txt).'</span>';
									$text .='<span>'.$this->highlightKeywords($cData['billing_telephone'], $txt).'</span>';
									$text .='<span>Website: '.$this->highlightKeywords($this->getWebsiteName($cData['website_id']), $txt).'</span></span>';
								$text .='</div>';
							}
						$text .='</div></div>';
					}
					$data=Array('status'  => 1, 'size' => $result->getSize(), 'text' => $text, 'data'=>$result->getData());
				break;
				
				case "email":
					$email			= $this->_request->getParam('email');
					/*
					$customerData 	= $this->_customer->getCollection()->addFieldToFilter('email', $email);
					*/
					$result=$this->_cusCol->create()->addFieldToFilter("email",['eq'=>$email]);
					if($result->getSize()==0){
						$data=Array('status'  => 1, 'size' => $result->getSize(),  'message' => '<span id="email_check_message"  class="admin__field-success">Email is usable</span>');
					}else{
						$data=Array('status'  => 0, 'size' => $result->getSize(), 'message' => '<span id="email_check_message" class="admin__field-error" for="new_customer_email">"'.$email.'" is already in use</span>','data'=>$result->getData());
					}
				break;
			}
			
			$assign = $this->getRequest()->getParam('assign');
			switch($assign){
				case "category":
					$id 		= $this->_request->getParam('id');
					$code 		= $this->_request->getParam('code');
					
					
					if($id && $code){
						$skuArray = $this->consignmentHelper->assignCategory($id, $code);
						if($skuArray){
							$size = count($skuArray);
							$data=Array('status'  => 1, 'size' => $size,'data'=>$skuArray , 'message' => $size .' products added to category');
						}else{
							$data=Array('status'  => 2, 'message' =>'No products found for assign process');
						}
						
					}else{
						$data=Array('status'  => 0, 'message' => '<strong class="txt-red">Error:</strong> Consignment <strong>Category</strong> and <strong>Source</strong> has to be set before assingning products!');
					}
				break;
				case "all":
					$counter = $this->consignmentHelper->assignCategoryAll();

					if($counter){
						$data=Array('status'  => 1, 'size' => $counter, 'message' => $counter .' products added to categories');
					}else{
						$data=Array('status'  => 2, 'message' =>'No products found for assign process');
					}
			}
			
			$result = $this->resultJsonFactory->create();
			$result->setData($data);
			return $result;
		}
		public function highlightKeywords($text, $keyword) {
			$wordsAry = explode(" ", $keyword);
			$wordsCount = count($wordsAry);
			
			for($i=0;$i<$wordsCount;$i++) {
				$highlighted_text = "<strong>$wordsAry[$i]</strong>";
				$text = str_ireplace($wordsAry[$i], $highlighted_text, $text);
			}

			return $text;
		}
		public function getWebsiteName($websiteId)
		{
			$collection = $this->_websiteModel->load($websiteId,'website_id');
			return $collection->getName();
		}
	}