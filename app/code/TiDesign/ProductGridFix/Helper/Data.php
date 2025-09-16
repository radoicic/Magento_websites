<?php
namespace TiDesign\ProductGridFix\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use TiDesign\Consignment\Model\ConsignmentlistFactory;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends AbstractHelper
{
	protected 	$_listFactory;
	protected 	$_websiteRepository;
	protected 	$_scopeConfig;
	protected 	$_storeManager;
    protected 	$websiteRepository;	
	
    public function __construct(
        Context 					$context,
		ConsignmentlistFactory 		$listFactory,
		WebsiteRepositoryInterface 	$websiteRepository,
		StoreManagerInterface 		$storeManager,
		ScopeConfigInterface 		$scopeConfig
    ){
		$this->_listFactory 		= $listFactory;
		$this->_websiteRepository 	= $websiteRepository;
		$this->_storeManager 		= $storeManager;
		$this->_scopeConfig			= $scopeConfig;
        parent::__construct($context);
    }	
	


	public function getData($source){
		$websiteId		= $this->_listFactory->create()->getCollection()
								->addFieldToFilter('td_source',array('eq' => $source))
								->getFirstItem()
								->getTdWebsite();
		if($websiteId){
			$websiteName	= $this->_websiteRepository->getById($websiteId)->getName();	
			return ['id'=> $websiteId, 'name'=> $websiteName];	//default website ekle
		}else{
			$default_source = $this->getConfig("consignment/consignment_status/default_source");
			if($source == $default_source){
				return $this->getDefaultWebsite();
			}else{
				return ['id'=> '', 'name'=> ''];
			}
			
		}
	}




	public function getConfig($config_path)
    {
		return $this->_scopeConfig->getValue(
            $config_path,
            ScopeInterface::SCOPE_STORE
        );
    }
    public function getDefaultWebsite()
    {
        $websiteId		= $this->_storeManager->getDefaultStoreView()->getWebsiteId();
        $websiteName	= $this->_websiteRepository->getById($websiteId)->getName();
		return ['id'=> $websiteId, 'name'=> $websiteName];
    }	
	public function getAllWebsites(){
		$s=[];
		foreach ($this->_storeManager->getWebsites() as $website) {
		  $s[] = ['id'=> $website->getId(), 'name'=> $website->getName()];
		}	
		return $s;
	}
}