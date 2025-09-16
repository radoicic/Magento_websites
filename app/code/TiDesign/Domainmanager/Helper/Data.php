<?php
namespace TiDesign\Domainmanager\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	private		$_scopeConfig;
	protected 	$_objectManager;
	protected 	$_storeManager;
	protected 	$_domainCollectionFactory;
	protected 	$_domainCollection;
	
	private 	$sourceItemsBySku;


	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\TiDesign\Domainmanager\Model\ResourceModel\Domainlist\CollectionFactory $domainCollectionFactory,
		\Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku
	){
        $this->_storeManager 				= $storeManager;
        $this->_objectManager 				= $objectManager;
		$this->_scopeConfig 				= $scopeConfig;	
		$this->_domainCollectionFactory 	= $domainCollectionFactory;		
		$this->sourceItemsBySku 			= $sourceItemsBySku;
		
	}
		
    public function getConfig($config_path)
    {
        return $this->_scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    protected function prepareCollection()
    {
        $this->_domainCollection = $this->_domainCollectionFactory->create()
			->addFieldToFilter('td_status', array('eq' => '1'));
    }
	public function getCollection($Url)
    {
        if (is_null($this->_domainCollection)) {
            $this->prepareCollection();
        }
		$staticCollection 	= clone $this->_domainCollection;
		$filteredCollection = $staticCollection->addFieldToFilter(['td_url'], 
			[
				['finset' => $Url]
			]);
		if($filteredCollection->count()>0){
			return $this->_storeManager->getStore($filteredCollection->getFirstItem()->getTdStore())->getCode();
		}else{
			return $this->_storeManager->getStore($this->getConfig("domainmanager/domainmanager_status/default_store"))->getCode();
		}
	}
	public function getCollectionData($Url)
    {
        if (is_null($this->_domainCollection)) {
            $this->prepareCollection();
        }
		$staticCollection 	= clone $this->_domainCollection;
		$filteredCollection = $staticCollection->addFieldToFilter(['td_url'], 
			[
				['finset' => $Url]
			]);
		if($filteredCollection->count()>0){
			return $filteredCollection->getFirstItem();
		}
	}
	
	public function getCategoryData()
	{
		$current_store = $this->_storeManager->getStore()->getCode();
		$secure_stores = explode(",",$this->getConfig('domainmanager/BitExpert_ForceCustomerLogin/restricted_store_list'));
		if (in_array($current_store, $secure_stores)) {

			$cid 			= $this->getCurrentCat()->getId();
			if (is_null($this->_domainCollection)) {
				$this->prepareCollection();
			}
			$sCollection 	= clone $this->_domainCollection;
			$fCollection 	= $sCollection->addFieldToFilter('td_category', array('eq' => $cid));
			if($fCollection->count()>0){
				return true;
			}
		}
	}
	public function getCurrentCat()
	{
		$objectManager 	= \Magento\Framework\App\ObjectManager::getInstance();
		$category 		= $objectManager->get('Magento\Framework\Registry')->registry('current_category');//get current category
		return $category;
	}
	public function isConsCat()
	{
	
		$current_store = $this->_storeManager->getStore()->getCode();
		$secure_stores = explode(",",$this->getConfig('domainmanager/BitExpert_ForceCustomerLogin/restricted_store_list'));
		if (in_array($current_store, $secure_stores)) {
			$objectManager 	= \Magento\Framework\App\ObjectManager::getInstance();
			$category 		= $objectManager->get('Magento\Framework\Registry')->registry('current_category');//get current category
			$cid 			= $category->getId();
			if (is_null($this->_domainCollection)) {
				$this->prepareCollection();
			}
			$sCollection 	= clone $this->_domainCollection;
			$fCollection 	= $sCollection->addFieldToFilter('td_category', array('eq' => $cid));
			if($fCollection->count()>0){
				return true;
			}
		}	
		return false;
	}
	public function getConsSource()
	{
		$objectManager 	= \Magento\Framework\App\ObjectManager::getInstance();
		$Url 				= $_SERVER['HTTP_HOST'];
        if (is_null($this->_domainCollection)) {
            $this->prepareCollection();
        }
		$staticCollection 	= clone $this->_domainCollection;
		$filteredCollection = $staticCollection->addFieldToFilter(['td_url'], 
			[
				['finset' => $Url]
			]);
		if($filteredCollection->count()>0){
			$data 				= $filteredCollection->getFirstItem();
			$m["store_id"] 		= $data->getTdStore();
			$m["consignment"]	= $data->getTdConsignment();
			$m["source"]		= $data->getTdSource();
			$m["category"]		= $data->getTdCategory();
			$m["cur_category"]	= $objectManager->get('Magento\Framework\Registry')->registry('current_category')->getId();
			return $data->getTdSource();
		}
		//return $m;
	}
	public function getConsStock($_product,$_source)
	{
		$sourceItemList = $this->sourceItemsBySku->execute($_product->getData(\Magento\Catalog\Api\Data\ProductInterface::SKU));
		foreach ($sourceItemList as $source) {
			$data = $source->getData();
			if($data["source_code"] == $_source){
				return (int)$data["quantity"];
			}	
		}
	}
	
	
}