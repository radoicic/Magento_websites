<?php
namespace TiDesign\CheckoutAgreements\Block\Checkout;

use Magento\Store\Model\ScopeInterface;

use Magento\Customer\Model\Session;
use TiDesign\CheckoutAgreements\Model\AgreementlistFactory;
use TiDesign\CheckoutAgreements\Helper\Data;
use TiDesign\CheckoutAgreements\Model\DataFactory;

use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Api\GroupRepositoryInterface;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Store\Model\StoreManagerInterface;

use Magento\Framework\Message\ManagerInterface;

class Agreements extends \Magento\Framework\View\Element\Template
{
	
    protected $_customerSession;
	protected $_agreementFactory;
	protected $_dataFactory;
	
    protected 	$_customer;
    protected 	$_customerFactory;
	protected 	$_groupRepository;	
	private 	$_filterProvider;
	private 	$storeManager;
	private 	$_url;
	private 	$_responseFactory;
	protected 	$_messageManager;
	
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		Session 				$customerSession,
		AgreementlistFactory 	$agreement,
		DataFactory 			$dataFactory,
		CustomerFactory 		$customerFactory,
		Customer 				$customers,
		GroupRepositoryInterface $groupRepository,
		FilterProvider 			$filter,
		StoreManagerInterface 	$storeManager,
		\Magento\Framework\UrlInterface $url, 
		\Magento\Framework\App\ResponseFactory $responseFactory,
		ManagerInterface $messageManager,
        array $data = []
    ) {
		$this->_customerSession 	= $customerSession;
		$this->_agreementFactory	= $agreement;
		$this->_dataFactory 		= $dataFactory;
		$this->_customerFactory = $customerFactory;
        $this->_customer 		= $customers;
		$this->_groupRepository = $groupRepository;
		$this->_filterProvider 	= $filter;
		$this->storeManager 	= $storeManager;
		$this->_url = $url;
		$this->_responseFactory = $responseFactory;
		$this->_messageManager = $messageManager;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getAgreements()
    {

		$agreements = [];
		if($this->_customerSession->isLoggedIn()) {
			$customer_id	= $this->_customerSession->getCustomerId();
			$customer_data 	= $this->getCustomer($customer_id);
			if($customer_data->getTidesignEnableContract() == 1){
				$store_id 		= $this->getStoreId();
				$customer_group = $customer_data->getGroupId();
				
				$settings 	= $this->_agreementFactory->create()->getCollection()
									->addFieldToFilter('is_active',array('eq' => 1))
									->addFieldToFilter(['customer_group'], 
										[
											['finset' => $customer_group]
										])
									->addFieldToFilter(['store_id'], 
										[
											['eq' => 0],
											['finset' => $store_id]
										])
										->getFirstItem();
						
				$agreements 	= $this->_dataFactory->create()->getCollection()
									->addFieldToFilter('customer_id',array('eq' => $customer_id))
									->getFirstItem();
									
				if($agreements){
					if($agreements->getStatus() != 1 || $agreements->getActivation() != 1 ){
						$this->_messageManager->addError('You must complete the terms and conditions procedure before you start shopping.');
						 //return $this->redirectFactory->create()
						//		->setPath('termsandconditions/contract/index');
						$CustomRedirectionUrl = $this->_url->getUrl('termsandconditions/contract/index');
						$this->_responseFactory->create()->setRedirect($CustomRedirectionUrl)->sendResponse();
						exit();	
					}
				}
				$agreements['settings'] = $settings;
			}
		}
				
        return $agreements;
    }
	
	protected function getCustomer($id)
    {
        return $this->_customerFactory->create()->getCollection()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter("entity_id", array("eq" => $id))
                ->load()->getFirstItem();
    }
	public function render($content){
		return $this->_filterProvider->getBlockFilter()->filter(trim($content));
	}
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }
}
