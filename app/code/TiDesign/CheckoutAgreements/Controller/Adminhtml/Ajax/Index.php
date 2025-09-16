<?php
	namespace TiDesign\CheckoutAgreements\Controller\Adminhtml\Ajax;
	
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
				case "acceptform":
					$customerid		= $this->_request->getParam('customerid');
					$logid 			= $this->_request->getParam('logid');			
					$data 		= $this->_logFactory->create()->getCollection()->addFieldToFilter('log_id',array('eq' => $logid))->getFirstItem();
					if($data){
						//save log
						$saveLog['log_id']		= $data->getLogId();	
						$saveLog['status'] 		= 1;
						$saveLog['operation'] 	= 0;
						$modelLog = $this->_logFactory->create()->load($logid);
						$modelLog->setData($saveLog)->save();
						
						//change contract status
						$data3 		= $this->_dataFactory->create()->getCollection()->addFieldToFilter('customer_id',array('eq' =>$customerid))->getFirstItem();
						if($data3){
							$saveData['id']				= $data3->getId();
							$saveData['activation']		= 0;		//aktivasyon kapalı
							$saveData['edit']			= 0;		//edit kapalı
							$saveData['status']			= 1;		//admin kabul etti
							
							$modelData = $this->_dataFactory->create()->load($data3->getId());
							$modelData->setData($saveData)->save();				
						}
						//send mail
						$data2 		= $this->_dataFactory->create()->getCollection()->addFieldToFilter('customer_id',array('eq' => $customerid))->getFirstItem();
						if($data2){
							$activation_code 	= $data2->getActivationCode();
							$filename 			= $data2->getFilename();
							
							$this->_mailHelper->sendConfirmationMail($customerid,$activation_code, $filename);
						}
						
						$data=Array('status'  => 1, 'html' => '<span class="msg-accept"><i aria-hidden="true" class="fa fa-check-circle-o"></i> Accepted</span>');
					}
				break;
				case "denyform":
					$customerid		= $this->_request->getParam('customerid');
					$logid 			= $this->_request->getParam('logid');	
					$message		= $this->_request->getParam('message');	
					
					$data 		= $this->_logFactory->create()->getCollection()->addFieldToFilter('log_id',array('eq' => $logid))->getFirstItem();
					if($data){
						//save log
						$saveLog['log_id']		= $data->getLogId();	
						$saveLog['status'] 		= 2;
						$saveLog['operation'] 	= 0;
						$modelLog = $this->_logFactory->create()->load($logid);
						$modelLog->setData($saveLog)->save();
						
						//change contract status
						$data3 		= $this->_dataFactory->create()->getCollection()->addFieldToFilter('customer_id',array('eq' =>$customerid))->getFirstItem();
						if($data3){
							$saveData['id']				= $data3->getId();
							$saveData['activation']		= 0;		//aktivasyon kapalı
							$saveData['edit']			= 1;		//edit kapalı
							$saveData['status']			= 0;		//admin kabul etti
							
							$modelData = $this->_dataFactory->create()->load($data3->getId());
							$modelData->setData($saveData)->save();				
						}						
						//send mail
						$filename 			= $data->getFilename();
						$this->_mailHelper->sendDenyMail($customerid, $filename, $message);
						
						$data=Array('status'  => 1, 'html' => '<span class="msg-deny"><i aria-hidden="true" class="fa fa-times "></i> Denied</span>');
					}					
				break;
				case "acceptedit":
					$customerid		= $this->_request->getParam('customerid');
					$logid 			= $this->_request->getParam('logid');			
					$data 		= $this->_logFactory->create()->getCollection()->addFieldToFilter('log_id',array('eq' => $logid))->getFirstItem();
					if($data){
						//save log
						$saveLog['log_id']		= $data->getLogId();	
						$saveLog['status'] 		= 1;
						$saveLog['operation'] 	= 0;
						$modelLog = $this->_logFactory->create()->load($logid);
						$modelLog->setData($saveLog)->save();
						
						//change contract status
						$data3 		= $this->_dataFactory->create()->getCollection()->addFieldToFilter('customer_id',array('eq' =>$customerid))->getFirstItem();
						if($data3){
							$saveData['id']				= $data3->getId();
							$saveData['activation']		= 0;		//aktivasyon kapalı
							$saveData['edit']			= 1;		//edit açık
							$saveData['status']			= 0;		//admin kabul kapalı
							
							$modelData = $this->_dataFactory->create()->load($data3->getId());
							$modelData->setData($saveData)->save();				
						}
						//send mail
						$this->_mailHelper->sendEditAcceptMail($customerid);
						
						$data=Array('status'  => 1, 'html' => '<span class="msg-accept"><i aria-hidden="true" class="fa fa-check-circle-o"></i> Accepted</span>');
					}
				break;
				case "denyedit":
					$customerid		= $this->_request->getParam('customerid');
					$logid 			= $this->_request->getParam('logid');	
					$message		= $this->_request->getParam('message');	
					
					$data 		= $this->_logFactory->create()->getCollection()->addFieldToFilter('log_id',array('eq' => $logid))->getFirstItem();
					if($data){
						//save log
						$saveLog['log_id']		= $data->getLogId();	
						$saveLog['status'] 		= 2;
						$saveLog['operation'] 	= 0;
						$modelLog = $this->_logFactory->create()->load($logid);
						$modelLog->setData($saveLog)->save();
						
						
						//send mail
						$this->_mailHelper->sendEditDenyMail($customerid, $message);
						
						$data=Array('status'  => 1, 'html' => '<span class="msg-deny"><i aria-hidden="true" class="fa fa-times "></i> Denied</span>');
					}					
				break;	
				case "deletecustomer":
					$customerid		= $this->_request->getParam('customerid');
					$logid 			= $this->_request->getParam('logid');	
					
					$data 		= $this->_logFactory->create()->getCollection()->addFieldToFilter('log_id',array('eq' => $logid))->getFirstItem();
					if($data){
						if($logid){
							//save log
							$saveLog['log_id']		= $data->getLogId();	
							$saveLog['status'] 		= 3;
							$saveLog['operation'] 	= 0;
							$modelLog = $this->_logFactory->create()->load($logid);
							$modelLog->setData($saveLog)->save();
						}

						$dataLog['customer_id'] 	= $customerid;
						$dataLog['message'] 		= "Customer data deleted by Admin";
						$dataLog['date'] 			= $this->_timezoneInterface->date()->format('Y-m-d H:i');
						$dataLog['status']			= 3;			
						$dataLog['operation']		= 0;	
						
						$logModel = $this->_logFactory->create();
						$logModel->setData($dataLog)->save();

						$modelData = $this->_dataFactory->create()->getCollection()->addFieldToFilter('customer_id',array('eq' => $customerid))->getFirstItem()->delete();

						
						//send mail
						//$this->_mailHelper->sendEditDenyMail($customerid, $message);
						
						$data=Array('status'  => 1);
					}					
				break;	
				case "manualactivate":
					$customerid		= $this->_request->getParam('customerid');
					$logid 			= $this->_request->getParam('logid');			
					$data 			= $this->_dataFactory->create()->getCollection()
											->addFieldToFilter('customer_id',array('eq' => $customerid))
											->addFieldToFilter('status',array('eq' => 1))
											->getFirstItem();
					if($data){

						//save log
						$dataLog['customer_id'] 	= $customerid;
						$dataLog['message'] 		= "Admin manually confirmed contract";
						//$dataLog['creation_ip'] 	= $this->_remoteAddress->getRemoteAddress();
						$dataLog['date'] 			= $this->_timezoneInterface->date()->format('Y-m-d H:i');
						$logModel = $this->_logFactory->create();
						$logModel->setData($dataLog)->save();
	

						$saveData['id']				= $data->getId();
						//$saveData['activation_ip'] 	= $this->_remoteAddress->getRemoteAddress();
						$saveData['activation']		= 1;
						
						$modelData = $this->_dataFactory->create()->load($data->getId());
						$modelData->setData($saveData)->save();				


						$data=Array('status'  => 1);						
					}
				break;				
			}

			$result = $this->resultJsonFactory->create();
			$result->setData($data);
			return $result;
		}

		
	}