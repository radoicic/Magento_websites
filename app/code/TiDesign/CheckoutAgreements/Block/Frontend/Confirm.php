<?php
namespace TiDesign\CheckoutAgreements\Block\Frontend;

use Magento\Framework\View\Element\Template;
use TiDesign\CheckoutAgreements\Model\DataFactory;
use TiDesign\CheckoutAgreements\Model\LogFactory;
use TiDesign\CheckoutAgreements\Helper\Mailer;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template\Context;

class Confirm extends Template
{
    protected $_customerSession;
    protected $_dataFactory;
	protected $_logFactory;
	protected $_remoteAddress;
	protected $_timezoneInterface;
	protected $_mailHelper;
	protected $_request;
	
	public function __construct(
		Context $context,
		\Magento\Customer\Model\Session 				$customerSession,
		DataFactory 									$dataFactory,
		LogFactory 										$logFactory,
		Mailer	 										$mailHelper,
		\Magento\Framework\App\Request\Http 			$request,
		RemoteAddress									$remoteAddress,
		TimezoneInterface 								$timezoneInterface,
		array $data = []
	) {
		$this->_customerSession 	= $customerSession;
		$this->_dataFactory 		= $dataFactory;
		$this->_logFactory 			= $logFactory;
		$this->_request 			= $request;
		$this->_remoteAddress 		= $remoteAddress;
		$this->_timezoneInterface 	= $timezoneInterface;
		$this->_mailHelper 			= $mailHelper;
		parent::__construct($context, $data);
	}

	public function getDefinitions()
	{
		$data = null; //  need to return at-least null
		if($this->_customerSession->isLoggedIn()) {
			$customerId	= $this->getCutomerId();
			$data 		= $this->_dataFactory->create()->getCollection()->addFieldToFilter('customer_id',array('eq' => $customerId))->getFirstItem();
		}
		return $data;
	}
	public function getCutomerId(){
		return $this->_customerSession->getCustomerId();
	}

	public function activate(){
		if (!empty($this->_request->getParam('id'))) {
			$postData['id']   				= $this->_request->getParam('id'); 
			$postData['activation_code']   	= $this->_request->getParam('key'); 
			$data 							= null;
			
			$data 	= $this->_dataFactory->create()->getCollection()
									->addFieldToFilter('customer_id',array('eq' => $postData['id']))
									->addFieldToFilter('activation_code',array('eq' => $postData['activation_code']))
									->addFieldToFilter('status',array('eq' => 1))
									->getFirstItem();
									
			//echo "<pre>".$data->getSize();/*print_r($data->debug());*/die();
			
			if($data->getId()){
				if($data->getActivation() == 0){
					
					$saveData['id']					= $data->getId();
					$saveData['activation_ip'] 		= $this->_remoteAddress->getRemoteAddress();
					$saveData['activation']			= 1;
					$model = $this->_dataFactory->create()->load($data->getId());
					$model->setData($saveData)->save();
					
					
					$dataLog['customer_id'] 	= $postData['id'];
					$dataLog['message'] 		= "Customer confirmed contract";
					$dataLog['creation_ip'] 	= $this->_remoteAddress->getRemoteAddress();
					$dataLog['date'] 			= $this->_timezoneInterface->date()->format('Y-m-d H:i');
					$logModel = $this->_logFactory->create();
					$logModel->setData($dataLog)->save();
					
					return 1; 	// actication done
				}else{
					return 2;	// already activated
				}
			}else{
				return 0;	// data not found
			}
			
		}
	}
}