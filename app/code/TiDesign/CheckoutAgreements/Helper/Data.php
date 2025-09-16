<?php
namespace TiDesign\CheckoutAgreements\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Store\Model\StoreManagerInterface;

use TiDesign\CheckoutAgreements\Model\AgreementlistFactory;

class Data extends AbstractHelper
{
	private 	$_filterProvider;
	protected 	$_listFactory;
    protected 	$_customer;
    protected 	$_customerFactory;
	protected 	$_groupRepository;
    /**
     * @var StoreManagerInterface
     */
	protected 	$_storeManager;
	
    public function __construct(
        Context 				$context,
		CustomerFactory 		$customerFactory,
		Customer 				$customers,
		GroupRepositoryInterface $groupRepository,
		AgreementlistFactory 	$listFactory,
		FilterProvider 			$filter,
		StoreManagerInterface 	$storeManager
    ){
		$this->_filterProvider 	= $filter;
		$this->_customerFactory = $customerFactory;
        $this->_customer 		= $customers;
		$this->_groupRepository = $groupRepository;
		$this->_listFactory 	= $listFactory;
		$this->_storeManager    = $storeManager;
        parent::__construct($context);
    }	
	


	public function getData($customer_id){
		$customer_data 	= $this->getCustomer($customer_id);
		$customer_group = $customer_data->getGroupId();
		$storeId		= $this->getStoreId();
		$code			= $this->_listFactory->create()->getCollection()
								->addFieldToFilter('is_active',array('eq' => 1))
								->addFieldToFilter(['customer_group'], 
								[
									['finset' => $customer_group]
								])
								->addFieldToFilter('store_id',array(
									array('finset'=> array('0')),
									array('finset'=> array($storeId)),
								))
								->getFirstItem()
								->getContent();
								
		$content = $this->_filterProvider->getBlockFilter()->filter(trim($code));						
		return $content;
	}
    protected function getCustomer($id)
    {
        return $this->_customerFactory->create()->getCollection()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter("entity_id", array("eq" => $id))
                ->load()->getFirstItem();
    }
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }	

}