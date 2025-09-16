<?php
namespace TiDesign\CheckoutAgreements\Block\Frontend;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use TiDesign\CheckoutAgreements\Model\DataFactory;
use TiDesign\CheckoutAgreements\Model\LogFactory;
use TiDesign\CheckoutAgreements\Model\AgreementlistFactory;
use TiDesign\CheckoutAgreements\Helper\Data;
use Magento\Framework\UrlInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Contract extends Template
{
    protected $_customerSession;
    protected $_dataFactory;
	protected $_agreementFactory;
	protected $_dataHelper; 
	protected $_urlBuilder;
	protected $_remoteAddress;
	protected $_logFactory;
	
	private	$scopeConfig;
	const CONTENT_HEIGHT 	= 'checkoutagreements/checkoutagreements_status/content_height';
    const BUTTON_TEXT 		= 'checkoutagreements/checkoutagreements_status/button_text';
    const ALWAYS_SHOW 		= 'checkoutagreements/checkoutagreements_status/always_show';
	
	public function __construct(
		Context 				$context,
		Session 				$customerSession,
		DataFactory 			$dataFactory,
		LogFactory 				$logFactory,
		AgreementlistFactory 	$agreement,
		Data 					$dataHelper,
		UrlInterface 			$urlBuilder,
		RemoteAddress			$remoteAddress,
		ScopeConfigInterface 	$scopeConfig,
		array 					$data = []
	) {
		$this->_customerSession 	= $customerSession;
		$this->_dataFactory 		= $dataFactory;
		$this->_logFactory 			= $logFactory;
		$this->_agreementFactory	= $agreement;
		$this->_dataHelper			= $dataHelper;
		$this->_urlBuilder 			= $urlBuilder;
		$this->_remoteAddress 		= $remoteAddress;
		$this->scopeConfig 			= $scopeConfig;
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
	public function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	public function getForm(){
		$code = null;
		if($this->_customerSession->isLoggedIn()) {
			$customerId		= $this->getCutomerId();
			$code			= $this->_dataHelper->getData($customerId);
		}
		return $code;
	}
	public function getDownloadUrl($file){
		$mediaPath = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
		return $mediaPath . 'TiDesign/contracts/'.$file;	
	}
	public function getCustomerIp(){
		return $this->_remoteAddress->getRemoteAddress();
	}
	public function getEditStatus(){
		$customerId	= $this->getCutomerId();
		$data 		= null;
		$data = $this->_logFactory->create()->getCollection()
								->addFieldToFilter('customer_id',array('eq' => $customerId))
								->addFieldToFilter('operation',array('eq' => 2))
								->addFieldToFilter('status',array('eq' => 0))
								->getFirstItem();

		if($data){
			if($data->getId()){
				return "disabled";
			}
		}
		return "";
	}
	
	
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	public function getContentHeight(){
		return $this->getConfig(self::CONTENT_HEIGHT);
	}
	public function getButtonText(){
		return $this->getConfig(self::BUTTON_TEXT);
	}
	public function getAlwaysShow(){
		return $this->getConfig(self::ALWAYS_SHOW);
	}	
	
}


