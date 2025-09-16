<?php
namespace TiDesign\CheckoutAgreements\Block\Customer;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Termslink extends \Magento\Framework\View\Element\Html\Link\Current
{
    protected $_customerSession;
	protected $scopeConfig;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context 	$context,
        \Magento\Framework\App\DefaultPathInterface 		$defaultPath,
        \Magento\Customer\Model\Session 					$customerSession,
		ScopeConfigInterface 								$scopeConfig,
        array $data = []
     ) {
         $this->_customerSession 	= $customerSession;
		 $this->scopeConfig 		= $scopeConfig;
         parent::__construct($context, $defaultPath, $data);
     }

    protected function _toHtml()
    {    
        $responseHtml = null; //  need to return at-least null
        if($this->_customerSession->isLoggedIn()) {
			$groups 		= explode(",", $this->getConfig('checkoutagreements/checkoutagreements_status/customer_group'));					
			$customerGroup	= $this->_customerSession->getCustomer()->getGroupId();
			$customerContract	= $this->_customerSession->getCustomer()->getTidesignEnableContract();
			
			if ( $customerContract==1 &&  in_array($customerGroup, $groups)) {
				$responseHtml 	= parent::_toHtml(); //Return link html
			}
        }
        return $responseHtml;
    }
	public function getConfig($config_path)
    {
		$storeId = $this->getStoreId();
		return $this->scopeConfig->getValue(
            $config_path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }		
}