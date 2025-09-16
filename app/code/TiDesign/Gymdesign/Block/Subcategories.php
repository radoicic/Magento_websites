<?php

namespace TiDesign\Gymdesign\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;


class Subcategories extends \Magento\Framework\View\Element\Template
{
    private $layerResolver;
    public 	$categoryRepository;
    public 	$categoryFactory;
	protected  	$scopeConfig;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
		ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        
        $this->layerResolver 		= $layerResolver;
        $this->categoryRepository 	= $categoryRepository;
        $this->categoryFactory 		= $categoryFactory; 
		$this->scopeConfig 			= $scopeConfig;
    }

	public function getContainer(){
		$container 	= $this->getConfig("gymdesign/gymdesign_setings/container_category_id");
		return $container;
	}
    public function getCurrentCategory() {
        return $this->layerResolver->get()->getCurrentCategory();
    }

    public function getCurrentCategoryId() {
        return $this->getCurrentCategory()->getId();
    }

    public function getCategoryData($id) {
        return $category = $this->categoryFactory->create()->load($id);       
    }
    
    public function getImageUrl($id) {
        $category = $this->categoryFactory->create()->load($id);
        return $category->getImageUrl();
    }
	
	public function getConfig($config_path)
	{
		return $this->scopeConfig->getValue(
			$config_path,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}
	public function getCategoryObject($container){
		$categoryObj = $this->categoryRepository->get($container);
		return $categoryObj;
	}
}