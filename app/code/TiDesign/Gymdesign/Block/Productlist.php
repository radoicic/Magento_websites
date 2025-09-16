<?php

namespace TiDesign\Gymdesign\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Productlist extends \Magento\Catalog\Block\Product\ListProduct {

    protected $_collection;

    protected $categoryRepository;

    protected $_resource;
	
	protected  	$scopeConfig;

    public function __construct(
            \Magento\Catalog\Block\Product\Context $context,
            \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
            \Magento\Catalog\Model\Layer\Resolver $layerResolver,
            CategoryRepositoryInterface $categoryRepository,
            \Magento\Framework\Url\Helper\Data $urlHelper,
            \Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
            \Magento\Framework\App\ResourceConnection $resource,
			ScopeConfigInterface $scopeConfig,
            array $data = []
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->_collection = $collection;
        $this->_resource = $resource;
		$this->scopeConfig 			= $scopeConfig;

        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }

    protected function _getProductCollection() {
        return $this->getProducts();
    }

    public function getProducts() {
        $count = $this->getProductCount();
        $category_id = $this->getContainer();
		
        $collection = clone $this->_collection;
        $collection->clear()->getSelect()->reset(\Magento\Framework\DB\Select::WHERE)->reset(\Magento\Framework\DB\Select::ORDER)->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)->reset(\Magento\Framework\DB\Select::GROUP);

        if(!$category_id) {
            $category_id = $this->_storeManager->getStore()->getRootCategoryId();
        }
        $category = $this->categoryRepository->get($category_id);
		$categories = $category->getAllChildren(true);
		
        if(isset($category) && $category) {
            $collection->addAttributeToSelect('name')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('thumbnail')
                ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
                ->addUrlRewrite()
				->distinct(true)
				->addCategoriesFilter(['in' => $category->getAllChildren(true)]);
        } else {
            $collection->addAttributeToSelect('name')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('thumbnail')
                ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
                ->addUrlRewrite() ;
        }

        $collection->getSelect()
            ->order('rand()')
            ->limit($count);

        return $collection;
    }

    public function getLoadedProductCollection() {
        return $this->getProducts();
    }

    public function getProductCount() {
        $limit = $this->getData("product_count");
        if(!$limit)
            $limit = 10;
        return $limit;
    }
	
	public function getConfig($config_path)
	{
		return $this->scopeConfig->getValue(
			$config_path,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}
	public function getContainer(){
		$container 	= $this->getConfig("gymdesign/gymdesign_setings/container_category_id");
		return $container;
	}
}
