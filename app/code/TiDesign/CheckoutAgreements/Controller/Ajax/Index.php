<?php
	namespace TiDesign\CheckoutAgreements\Controller\Ajax;
	
	use Magento\Framework\App\Action\Action;
	use Magento\Framework\App\ResponseInterface;
	use Magento\Framework\Controller\ResultFactory;
	use Magento\Framework\App\Config\ScopeConfigInterface;
	use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
	
	class Index extends \Magento\Framework\App\Action\Action
	{
		protected 	$_mailHelper;
		protected 	$_dataHelper;
		protected 	$_dataFactory;
		protected 	$_logFactory;		
		protected 	$_timezoneInterface;
		
		public function __construct(
			\Magento\Framework\App\Action\Context 				$context,
			\TiDesign\CheckoutAgreements\Helper\Mailer 			$mailHelper,
			\TiDesign\CheckoutAgreements\Helper\Data 			$dataHelper,
			\TiDesign\CheckoutAgreements\Model\DataFactory 		$dataFactory,
			\TiDesign\CheckoutAgreements\Model\LogFactory 		$logFactory,
			TimezoneInterface 									$timezoneInterface,
			\Magento\Framework\Controller\Result\JsonFactory 	$resultJsonFactory
		) {
			parent::__construct($context);
			$this->_mailHelper 				= $mailHelper;
			$this->_dataHelper 				= $dataHelper;
			$this->_dataFactory 			= $dataFactory;
			$this->_logFactory 				= $logFactory;
			$this->_timezoneInterface 		= $timezoneInterface;
			$this->resultJsonFactory 		= $resultJsonFactory;
		}
		public function execute() {
			$data 		= null;
			$data2		= null;
			$data3		= null;
			$saveData	= null;
			$saveLog	= null;
			
			$islem 		= $this->getRequest()->getParam('islem');
			
			switch($islem){
				case "requestedit":
					$customerid		= $this->_request->getParam('customerid');
					$ip				= $this->_request->getParam('ip');
						//save log
						$saveLog['customer_id']	= $customerid;
						$saveLog['message']		= "Customer requested edit";
						$saveLog['date'] 		= $this->_timezoneInterface->date()->format('Y-m-d H:i');
						$saveLog['creation_ip'] = $ip;
						$saveLog['status'] 		= 0;
						$saveLog['operation'] 	= 2;
						$modelLog = $this->_logFactory->create();
						$modelLog->setData($saveLog)->save();
						
						//send mail
						$this->_mailHelper->sendAdminInfoMail();
						
						$data=Array('status'  => 1, 'html' => 'Your edit request has been sent to the administrator. You will be notified via e-mail when your request is answered.');
				break;				
			}

			$result = $this->resultJsonFactory->create();
			$result->setData($data);
			return $result;
		}

		
	}