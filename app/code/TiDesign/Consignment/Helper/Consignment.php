<?php
namespace TiDesign\Consignment\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;

use Magento\Catalog\Model\ProductFactory;

class Consignment extends AbstractHelper
{
	private 	$_consCollection;
	protected 	$_logger;
	protected  	$scopeConfig;
	protected 	$categoryCollectionFactory;
	protected 	$categoryFactory;
    protected 	$_storeManager;
	protected 	$customerRepository;
	private 	$sourceRepository;
	private 	$sourceItemsBySku;
	protected 	$_resource;
	protected 	$_productRepository;
	protected 	$_categoryLinkManagement;
    protected   $_customerSession;
	protected 	$websiteCollectionFactory;

    protected 	$productFactory;

	public function __construct(
		Context $context,
		\TiDesign\Consignment\Model\ConsignmentlistFactory $consCollection,
		\TiDesign\Consignment\Logger\Logger $customLogger,
		ScopeConfigInterface $scopeConfig,
		\Magento\Catalog\Model\CategoryRepository $categoryCollectionFactory,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		\Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku,
		\Magento\Framework\App\ResourceConnection $resource,
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		\Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagement,
		SourceRepositoryInterface $sourceRepository,
		WebsiteCollectionFactory $websiteCollectionFactory,
        ProductFactory $productFactory,
        \Magento\Customer\Model\Session $customerSession
	) {
		$this->_consCollection 				= $consCollection;
		$this->_logger 						= $customLogger;
		$this->scopeConfig 					= $scopeConfig;
		$this->categoryCollectionFactory 	= $categoryCollectionFactory;
		$this->categoryFactory 				= $categoryFactory;
		$this->_storeManager 				= $storeManager;
		$this->customerRepository 			= $customerRepository;
		$this->sourceRepository 			= $sourceRepository;
        $this->_customerSession             = $customerSession;
        $this->sourceItemsBySku 			= $sourceItemsBySku;
		$this->_productRepository 			= $productRepository;
		$this->_categoryLinkManagement 		= $categoryLinkManagement;
		$this->_resource 					= $resource;
		$this->websiteCollectionFactory 	= $websiteCollectionFactory;
        $this->productFactory               = $productFactory;
		parent::__construct($context);
    }

	function seoString($vp_string){
		$vp_string = trim($vp_string);
		$vp_string = html_entity_decode($vp_string);
		$vp_string = strip_tags($vp_string);
		$vp_string = strtolower($vp_string);
		$vp_string = preg_replace('~[^ a-z0-9_\.]~', ' ', $vp_string);
		$vp_string = preg_replace('~ ~', '-', $vp_string);
		$vp_string = preg_replace('~-+~', '-', $vp_string);
		return $vp_string;
		}

	public function getConfig($config_path)
	{
		return $this->scopeConfig->getValue(
			$config_path,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}


	public function checkName($name, $id)
	{
		$data 	= $this->_consCollection->create()->getCollection();
		$data->addFieldToFilter('td_name', ['eq'=>$name])->addFieldToFilter('td_id', ['neq'=>$id]);
		return $data->getSize();
	}
	public function checkCategory($name,$website)
	{
		$urlKey		= $this->seoString($name);
		$data		= $this->getConsignmentCategories($website);
		$catArray	= $data["cat"];
		$urlArray	= $data["url"];

		if(in_array($name, $catArray) ||  in_array($urlKey,$urlArray)) {
			return false;
		}
		return true;
	}
	public function getConsignmentCategories($website = null, $returnsub = false){
		$dataArray		= [];
		$catArray 		= [];
		$urlArray 		= [];
		$categoryId		= $this->getWebsiteConfig("consignment/consignment_status/container_category_id",$website);
		$category 		= $this->categoryCollectionFactory->get($categoryId);
		$subCategories 	= $category->getChildrenCategories();
		$i = 0;
		if($returnsub == true){
			return $subCategories;
		}
		foreach($subCategories as $subcategory){
			$catArray[$i]	= $subcategory->getName();
			$urlArray[$i]	= $subcategory->getUrlKey();
			$i++;
		}
		$dataArray["cat"] = $catArray;
		$dataArray["url"] = $urlArray;
		return $dataArray;
	}
	public function getConsignmentFromCategory($categoryId){
		$data 	= $this->_consCollection->create()->getCollection();
		$data->addFieldToFilter('td_category', ['eq'=>$categoryId])->getFirstItem()->toArray();
		//echo "<pre>";print_r($data);echo "</pre>";die;
		return $data;
	}

	public function getFilterCategories(){
		$websites		= $this->getWebsites();
		$coll			= [];
		foreach($websites as $website) {
			$websiteId 					= $website->getWebsiteId();
			$categoryId					= $this->getWebsiteConfig("consignment/consignment_status/container_category_id",$websiteId);
			$category 					= $this->categoryCollectionFactory->get($categoryId);
			$ss							= $category->getChildrenCategories();
			foreach( $ss as $subcat){
				$coll[$subcat->getId()]['name'] 	= $subcat->getName();
				$coll[$subcat->getId()]['count'] = $subcat->getProductCount();
				$coll[$subcat->getId()]['id'] 	= $subcat->getId();
			}
		}
		return $coll;
	}
	public function getWebsites()
	{
		return $this->websiteCollectionFactory->create();
	}
	public function isConsignmentCategory($categoryId){
		$data 	= $this->_consCollection->create()->getCollection();
		$data->addFieldToFilter('td_category', ['eq'=>$categoryId]);
		if($data->getSize()){
			return true;
		}
		return false;
	}
	public function isUserHasPermission($categoryId,$userId){
		$data 	= $this->_consCollection->create()->getCollection();
		$data->addFieldToFilter('td_category', ['eq'=>$categoryId]);
		$data->addFieldToFilter('td_customer', ['eq'=>$userId]);

//		echo "categoryId: ".$categoryId."<br>";
//		echo "userId: ".$userId."<br>";
//		echo "<pre>";print_r($data->getData());echo "</pre>";die;
		if($data->getSize()){
			return true;
		}
		return false;
	}
	public function getPrivateLink($userId){
		$consUserGroup		= $this->getConfig("consignment/consignment_status/customer_group");
		$consUserGroups		= explode(",", $this->getConfig("consignment/consignment_status/customer_groups"));

		$currentCustomer 	= $this->customerRepository->getById($userId);
		$currentGroup		= $currentCustomer->getGroupId();

		if( $currentGroup == $consUserGroup || in_array($currentGroup, $consUserGroups )){

			$data 	= $this->_consCollection->create()->getCollection();
			$data->addFieldToFilter('td_customer', ['eq'=>$userId])->getFirstItem()->toArray();
			if($data->getSize()){
				$dd = $data->getFirstItem()->toArray();

				$sourceCode = $dd["td_source"];
				$sourceInfo = $this->sourceRepository->get($sourceCode);
				if($sourceInfo){
					if($sourceInfo->isEnabled()==true){
						$categoryId = $dd["td_category"];
						$category = $this->categoryCollectionFactory->get($categoryId, $this->_storeManager->getStore()->getId());
						$limit = $this->getConfig("consignment/consignment_status/list_count");
						return $category->getUrl() . "?product_list_limit=".$limit."&product_list_order=name";
					}
				}

			}
		}
	}
	public function getCategoryId($consid){
		$data 	= $this->_consCollection->create()->getCollection();
		$data->addFieldToFilter('td_id', ['eq'=>$consid]);
		if($data->getSize()){
			$dd = $data->getFirstItem()->toArray();
			$categoryid = $dd["td_category"];
			return $categoryid;
		}
	}
	public function getConsStock($_product,$_source)
	{
		if($_product->getTypeID() == 'configurable'){
			$total_stock = 0;
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$StockState = $objectManager->get('\Magento\CatalogInventory\Api\StockStateInterface');

			$productTypeInstance = $_product->getTypeInstance();
			$usedProducts = $productTypeInstance->getUsedProducts($_product);
			foreach ($usedProducts as $simple) {
				//$total_stock += $StockState->getStockQty($simple->getId(), $simple->getStore()->getWebsiteId());
				$sourceItemList = $this->sourceItemsBySku->execute($simple->getData(\Magento\Catalog\Api\Data\ProductInterface::SKU));
				foreach ($sourceItemList as $source) {
					$data = $source->getData();
					if($data["source_code"] == $_source){
						$total_stock +=  (int)$data["quantity"];
					}
				}
			}
			return $total_stock;
		}else{
			$sourceItemList = $this->sourceItemsBySku->execute($_product->getData(\Magento\Catalog\Api\Data\ProductInterface::SKU));
			foreach ($sourceItemList as $source) {
				$data = $source->getData();
				if($data["source_code"] == $_source){
					return (int)$data["quantity"];
				}
			}
		}
	}
 	public function getAllStock($sku)
	{
		$list =[];
		$sourceItemList = $this->sourceItemsBySku->execute($sku);
		foreach ($sourceItemList as $source) {
			$data = $source->getData();
			$list[$data["source_code"]] = (int)$data["quantity"];
		}
		return $list;
	}
	public function getConsSource($categoryId){
		$data 	= $this->_consCollection->create()->getCollection();
		$data->addFieldToFilter('td_category', ['eq'=>$categoryId])->getFirstItem()->toArray();
		if($data->getSize()){
			$dd = $data->getFirstItem()->toArray();
			return   $dd["td_source"];
		}
		return false;
	}
	public function getDefaultSource(){
		return $this->getConfig("consignment/consignment_status/default_source");
	}
	public function assignCategory($categoryId, $sourceCode){
		$connection = $this->_resource->getConnection();
		$tableName 	= $connection->getTableName('products_source_inventory_stock');

		$catsql = "SELECT * FROM catalog_category_entity WHERE entity_id =" .$categoryId;
		$cate 	= $connection->fetchAll($catsql);


		$sql 		= "SELECT * FROM " . $tableName . " WHERE	source_code = '".$sourceCode."'AND (NOT FIND_IN_SET( ".$categoryId.", categories )  OR categories IS NULL )	AND quantity > 0";
		$results 	= [];
		$products 	= $connection->fetchAll($sql);
		if(count($products)){
			$size 			= count($products);
			$skuArray		= array_column($products, 'sku');
			foreach($skuArray as $sku){
				$categoryIds 	= [];
				$product 		= $this->_productRepository->get($sku);
				$categoryIds 	= $product->getCategoryIds();
				if(count($cate)){
					$categoryIds[] 	= $categoryId;
				}
				$this->_categoryLinkManagement->assignProductToCategories($sku, $categoryIds);
			}
			return $skuArray;
		}else{
			return false;
		}
	}
	public function assignCategoryAll(){
		$connection = $this->_resource->getConnection();
		$tableName 	= $connection->getTableName('products_source_inventory_stock');


		$data 	= $this->_consCollection->create()->getCollection();
//		return json_encode($data->toArray());
		if($data->getSize()){
			try {
				$counter 	= 0;
				$result 	= [];
				$consData 	= $data->getData();
				foreach ($consData as $cons){
					$categoryId = $cons["td_category"];
					$sourceCode = $cons["td_source"];

					if($cons["td_status"] == 1){
//						echo $categoryId ."-".$sourceCode."\n";
/* 						$result = $this->assignCategory($categoryId, $sourceCode);
						if($result) $counter +=count($result);
 */
						$catsql = "SELECT * FROM catalog_category_entity WHERE entity_id =" .$categoryId;
						$cate 	= $connection->fetchAll($catsql);


						$sql 		= "SELECT * FROM " . $tableName . " WHERE	source_code = '".$sourceCode."'AND (NOT FIND_IN_SET( ".$categoryId.", categories )  OR categories IS NULL )	AND quantity > 0";
						$results 	= [];
						$products 	= $connection->fetchAll($sql);
//						echo "products: ".count($products)."\n";
						if(count($products)){
							$size 			= count($products);
							$skuArray		= array_column($products, 'sku');
//							echo "skuArray: ".json_encode($skuArray)."\n";
							foreach($skuArray as $skux){
//								echo $skux."\n";




								$categoryIds 	= [];
								$product 		= $this->_productRepository->get($skux);
								$categoryIds 	= $product->getCategoryIds();




								if(count($cate)){
									$categoryIds[] 	= $categoryId;
								}

//								echo "categoryIds: ".json_encode($categoryIds)."\n";
								$this->_categoryLinkManagement->assignProductToCategories($skux, $categoryIds);

							}
							//return $skuArray;
							$counter +=count($skuArray);
						}

					}
				}
				$this->_logger->error($categoryId ."-".$sourceCode."-".$counter);
				return $counter;
			} catch (\Magento\Framework\Exception\LocalizedException $e) {
					$this->_logger->error('assignCategoryAll error -> ' .  $e->getMessage());
			}
		}
		return false;
	}
	public function compareUrl($url){
		$categoryId		= $this->getConfig("consignment/consignment_status/container_category_id");
		$category 		= $this->categoryCollectionFactory->get($categoryId);
		$categoryUrl	= $category->getUrlKey();
		if (strpos($url,$categoryUrl) !== false) {
			return true;
		}
		return false;
	}
    public function getDisplayAgreement(){
        $customerId = $this->_customerSession->getId();
        if($customerId){
            $consUserGroup		= $this->getConfig("consignment/consignment_status/customer_group");
			$consUserGroups		= explode(",", $this->getConfig("consignment/consignment_status/customer_groups"));

            $currentCustomer 	= $this->customerRepository->getById($customerId);
            $currentGroup		= $currentCustomer->getGroupId();
            if( $currentGroup == $consUserGroup || in_array($currentGroup, $consUserGroups )){
                return "";
            }
        }
        return "none";
    }
	public function getWebsiteConfig($config_path, $wesiteId)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES,
			$wesiteId
        );
    }


	public function getDisabledProducts($consid){
		$data 	= $this->_consCollection->create()->getCollection();
		$data->addFieldToFilter('td_id', ['eq'=>$consid]);
		if($data->getSize()){
			$dd = $data->getFirstItem()->toArray();
			$disabledProducts = $dd["td_disabled"];
			return $disabledProducts;
		}
	}

	public function getDisabledCount($consid){
		$data 	= $this->_consCollection->create()->getCollection();
		$data->addFieldToFilter('td_id', ['eq'=>$consid]);
		if($data->getSize()){
			$dd = $data->getFirstItem()->toArray();
			$disabledProducts = array_filter(explode(',',$dd["td_disabled"]));


            $store = $this->_storeManager->getStore();
            $collection = $this->productFactory->create()->getCollection()->addAttributeToSelect(
                '*'
            )->setStore(
                $store
            );

            $categoryId = $this->getCategoryId($consid);
            if($categoryId){
                $collection->addCategoriesFilter(['in' => $categoryId]);
            }
            $collection->addAttributeToFilter('entity_id',['in'=>$disabledProducts]);

			return count($collection->getAllIds());
			//return count($disabledProducts);
		}
	}

}
