<?php
namespace TiDesign\Consignment\Block\Adminhtml\Consignmentlist;

use Magento\Backend\Block\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;

class Form extends Template
{
	private 	$scopeConfig;
	protected 	$categoryCollectionFactory;
	private 	$_consCollection;
	protected 	$_cusCol;
	private 	$sourceRepository;
	protected 	$_countryCollectionFactory;
	protected 	$websiteCollectionFactory;
	protected 	$_websiteModel;
    /**
    * @param Context $context
    * @param array $data
    */
    public function __construct(
        Template\Context $context,
		\TiDesign\Consignment\Model\ConsignmentlistFactory $consCollection,
		ScopeConfigInterface $scopeConfig,
		\Magento\Catalog\Model\CategoryRepository $categoryCollectionFactory,
		SourceRepositoryInterface $sourceRepository,
		\Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
		\Magento\Customer\Model\ResourceModel\Grid\CollectionFactory $cusCol,
		WebsiteCollectionFactory $websiteCollectionFactory,
		\Magento\Store\Model\Website $websiteModel,
        array $data = []
    ) {
		$this->_consCollection 	= $consCollection;
		$this->categoryCollectionFactory = $categoryCollectionFactory;
		$this->scopeConfig 		= $scopeConfig;
		$this->sourceRepository = $sourceRepository;
		$this->_countryCollectionFactory = $countryCollectionFactory;
        $this->_cusCol = $cusCol;
		$this->websiteCollectionFactory = $websiteCollectionFactory;
		$this->_websiteModel = $websiteModel;
        parent::__construct($context, $data);
    }
	public function getFormData(){
		/*
		$data = $this->_consCollection->create()->getCollection();
		$idsi = $this->_request->getParam('id');
		$data->addFieldToFilter('td_id', ['eq'=>$idsi]);
		if($data->getSize() == 0){
			return null;
		}
		return $data;
		*/
		$text		= "";
        $idsi 		= $this->getRequest()->getParam('id');
		$data		= $this->_consCollection->create()->load($idsi);
		$cid		= $data->getTdCustomer();
		$customer 	= $this->_cusCol->create()->addFieldToFilter("entity_id", array("eq" => $cid));

		if($customer->getSize()){
			$cData = $customer->getFirstItem()->getData();
			$text ='<div class="s-container preselected"><div class="s-table"><label>Current selected user</label>';
			$text .='<div class="c-cell">';
			$text .='<span class="inputc"><input type="radio" class="customer-selector" name="customer-selector" value="'.$cData['entity_id'].'" checked></span>';
			$text .='<span><span>'.$cData['name'].'</span>';
			$text .='<span>'.$cData['email'].'</span>';
			$text .='<span>'.$cData['billing_street'].'</span>';
			$text .='<span>'.$cData['billing_city'].', '.$cData['billing_postcode'].'</span>';
			$text .='<span>'.$cData['billing_region'].'</span>';
			$text .='<span>'.$cData['billing_telephone'].'</span>';
			$text .='<span>Website: '.$this->getWebsiteName($cData['website_id']).'</span></span>';
			$text .='</div></div></div>';
		}
		$data["customer-extra"] = $text;
        return $data;
		
	}
	public function getWebsites()
	{
		return $this->websiteCollectionFactory->create();
	}
	public function getWebsiteName($websiteId)
    {
        $collection = $this->_websiteModel->load($websiteId,'website_id');
        return $collection->getName();
    }
	public function getCategories()
	{
		$websites		= $this->getWebsites();
		$coll		= [];
		foreach($websites as $website) {
			$websiteId = $website->getWebsiteId();
			$categoryId		= $this->getConfig("consignment/consignment_status/container_category_id",$websiteId );
			$cat = $this->getCategories2($categoryId);
			$coll[$websiteId] = $cat;
		}
		return $coll;
	}
	public function getCategories2($categoryId)
	{
		$category 		= $this->categoryCollectionFactory->get($categoryId);
		$subCategories 	= $category->getChildrenCategories();
		return $subCategories;
	}
	public function getSources(){
		$options = [];
		try {
			$sourceData = $this->sourceRepository->getList()->getItems();
			foreach ($sourceData as $source) {
				$options[] = ['label' => $source->getName(), 'value' => $source->getSourceCode()];
			}
			
		}catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
		return $options;
	}
	public function getCountries()
	{
		$collection = $this->_countryCollectionFactory->create()->loadByStore();
		return $collection;
	}
    public function getConfig($config_path, $wesiteId)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES,
			$wesiteId
        );
    }
}