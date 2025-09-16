<?php
/**
 * Created By : Rohan Hapani
 */
namespace TiDesign\Gymdesign\Plugin\Catalog\Controller\Category;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Category\Attribute\LayoutUpdateManager;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Registry;

class View
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
	
	protected  	$scopeConfig;
	public 	$categoryFactory;
	private $registry;

    /**
     * @param RequestInterface           $request
     * @param StoreManagerInterface      $storeManager
     */
    public function __construct(
        RequestInterface $request,
        StoreManagerInterface $storeManager,
		ScopeConfigInterface $scopeConfig,
		CategoryFactory $categoryFactory,
		Registry $registry
    ) {
        $this->request 				= $request;
        $this->storeManager 		= $storeManager;
		$this->scopeConfig 			= $scopeConfig;
		$this->categoryFactory 		= $categoryFactory; 
		$this->registry 			= $registry;
    }

    public function afterExecute(\Magento\Catalog\Controller\Category\View $subject, $resultPage)
    {
        if ($resultPage instanceof ResultInterface)
        {
			$category = $this->registry->registry('current_category');
			
			//$categoryId = (int)$this->getRequest()->getParam('id', false);			
            if ($category)
            {
                try
                {		
					$categoryId	= $category->getId();
					$container 	= $this->getConfig("gymdesign/gymdesign_setings/container_category_id");
					$stat		= $this->compareCategory($categoryId, $container);
					if ($stat) {

						$pageConfig = $resultPage->getConfig();
                        $pageConfig->setPageLayout('2columns-left'); //Set your page layout here.
                    
					}						
				
                }
                catch (NoSuchEntityException $e)
                {
                    // Add you exception message here.
                }
            }			
			
			
        }
        return $resultPage;
    }
	
	public function getConfig($config_path)
	{
		return $this->scopeConfig->getValue(
			$config_path,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}

	private function compareCategory($category, $container){
		$cc = $this->categoryFactory->create()->load($category); 
		
		if(
			($category == $container) ||
			($cc->getParentCategory()->getId() == $container)
		){
			return true;
		}
		return false;
		
	}
}