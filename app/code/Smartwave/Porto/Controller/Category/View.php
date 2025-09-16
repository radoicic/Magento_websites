<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Smartwave\Porto\Controller\Category;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;

class View extends \Magento\Catalog\Controller\Category\View
{
	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog session
     *
     * @var \Magento\Catalog\Model\Session
     */
    protected $_catalogSession;

    /**
     * Catalog design
     *
     * @var \Magento\Catalog\Model\Design
     */
    protected $_catalogDesign;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator
     */
    protected $categoryUrlPathGenerator;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;
	
	protected $_scopeConfig;
	
	private $consignmentHelper;
	private $customerSession;
	private $customerRepository;
	protected $groupRepository;
	
	
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Catalog\Model\Design $catalogDesign
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\Design $catalogDesign,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		CustomerRepositoryInterface $customerRepository,
		CustomerSession $customerSession,
		\Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
		\TiDesign\Consignment\Helper\Consignment $consignmentHelper
    ) {
        parent::__construct($context, $catalogDesign, $catalogSession, $coreRegistry, $storeManager, $categoryUrlPathGenerator, $resultPageFactory, $resultForwardFactory, $layerResolver, $categoryRepository);
        $this->_storeManager = $storeManager;
        $this->_catalogDesign = $catalogDesign;
        $this->_catalogSession = $catalogSession;
        $this->_coreRegistry = $coreRegistry;
        $this->categoryUrlPathGenerator = $categoryUrlPathGenerator;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->layerResolver = $layerResolver;
        $this->categoryRepository = $categoryRepository;
		$this->_scopeConfig = $scopeConfig;
		$this->customerRepository = $customerRepository;
		$this->customerSession = $customerSession;
		$this->groupRepository = $groupRepository;
		$this->consignmentHelper = $consignmentHelper;
    }
	
    /**
     * Category view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
		if ($this->_request->getParam(\Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED)) {
			return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRedirectUrl());
		}
		$category = $this->_initCategory();
		if ($category) {
			$this->layerResolver->create(Resolver::CATALOG_LAYER_CATEGORY);
			$settings = $this->_catalogDesign->getDesignSettings($category);

			// apply custom design
			if ($settings->getCustomDesign()) {
				$this->_catalogDesign->applyCustomDesign($settings->getCustomDesign());
			}

			$this->_catalogSession->setLastViewedCategoryId($category->getId());

			$page = $this->resultPageFactory->create();
			// apply custom layout (page) template once the blocks are generated
			
			if ($settings->getPageLayout()) {
				$page->getConfig()->setPageLayout($settings->getPageLayout());
			}else{
				$panelLayout = $this->_scopeConfig->getValue('porto_settings/category/page_layout', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
				if($panelLayout!=''){
					$page->getConfig()->setPageLayout($panelLayout);
				}
			}
			
/*----------------------------------------------------------------------------------------------------------------------------------------------------- */
	
/*			
			if($this->_domainHelper->getCategoryData()){
				$page->getConfig()->setPageLayout('2columns-left');
				$toolbar =  $page->getLayout()->getBlock('product_list_toolbar');
				if($toolbar) {
					$toolbar = $toolbar->setData('_current_grid_mode', 'list');
					$toolbar = $toolbar->setData('_current_limit', '100');
					$toolbar = $toolbar->disableViewSwitcher();
				}		
			}
*/
/*
echo "container id: " .$this->consignmentHelper->getConfig("consignment/consignment_status/container_category_id") ."-- category id: ". $category->getId() ."<br>";
echo "is consignment category: ".$this->consignmentHelper->isConsignmentCategory($category->getId())."<br>";
echo "is loggedin: ".$this->customerSession->isLoggedIn()."<br>";
echo "is consignment customer: ".$this->isConsignmentCustomer()."<br>";
echo "customer id: ". $this->customerSession->getId()."<br>";
echo "has permission:" . $this->consignmentHelper->isUserHasPermission($category->getId() , $this->customerSession->getId())."<br>";
die();
*/


			//not rootcat
			if($this->consignmentHelper->getConfig("consignment/consignment_status/container_category_id") == $category->getId() )  {
				$this->_redirect('consignment-restricted-notice');
				return;
			}
			
			//category cons
			//login
			//user cons
			//user permission
			//source active
			if( 
				$this->consignmentHelper->isConsignmentCategory($category->getId()) && (
				!$this->customerSession instanceof \Magento\Customer\Model\Session && $this->customerSession->isLoggedIn() ||
				!$this->isConsignmentCustomer() ||
				!$this->consignmentHelper->isUserHasPermission($category->getId() , $this->customerSession->getId()) ||
				!$this->consignmentHelper->getPrivateLink($this->customerSession->getId())
				)
			){
				$this->_redirect('consignment-restricted-notice');
				return;
			}
			
			if ($this->consignmentHelper->isConsignmentCategory($category->getId())){
				$page->getConfig()->setPageLayout('2columns-left');
				$toolbar =  $page->getLayout()->getBlock('product_list_toolbar');
				if($toolbar) {
					$toolbar = $toolbar->setData('_current_grid_mode', 'list');
					//$toolbar = $toolbar->setData('_current_limit', '100');
					if (isset($_GET['product_list_limit'])) {
						$limit = $_GET["product_list_limit"];
					}else{
						$limit = null;
					}
					if(!$limit){
						$limit = $this->consignmentHelper->getConfig("consignment/consignment_status/list_count");
					}
					$toolbar = $toolbar->setData('_current_limit', $limit);
					$toolbar = $toolbar->disableViewSwitcher();
				}				
			}
			
/*----------------------------------------------------------------------------------------------------------------------------------------------------- */			
			
			
			
			

			$hasChildren = $category->hasChildren();
			if ($category->getIsAnchor()) {
				$type = $hasChildren ? 'layered' : 'layered_without_children';
			} else {
				$type = $hasChildren ? 'default' : 'default_without_children';
			}

			if (!$hasChildren) {
				// Two levels removed from parent.  Need to add default page type.
				$parentType = strtok($type, '_');
				$page->addPageLayoutHandles(['type' => $parentType]);
			}
			$page->addPageLayoutHandles(['type' => $type, 'id' => $category->getId()]);

			// apply custom layout update once layout is loaded
			$layoutUpdates = $settings->getLayoutUpdates();
			if ($layoutUpdates && is_array($layoutUpdates)) {
				foreach ($layoutUpdates as $layoutUpdate) {
					$page->addUpdate($layoutUpdate);
					$page->addPageLayoutHandles(['layout_update' => md5($layoutUpdate)]);
				}
			}
            $full_width = $this->_scopeConfig->getValue('porto_settings/general/layout', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
            $additional_class = '';
            if(isset($full_width) && $full_width == 'full_width')
                $additional_class = 'layout-fullwidth';
			$page->getConfig()->addBodyClass('page-products')
				->addBodyClass('categorypath-' . $this->categoryUrlPathGenerator->getUrlPath($category))
				->addBodyClass('category-' . $category->getUrlKey())
                ->addBodyClass($additional_class);

			return $page;
		} elseif (!$this->getResponse()->isRedirect()) {
			return $this->resultForwardFactory->create()->forward('noroute');
		}
    }
	private function isConsignmentCustomer(){
		$customerId  = $this->customerSession->getCustomer()->getId();
		$consUserGroup	= $this->consignmentHelper->getConfig("consignment/consignment_status/customer_group");
		$consUserGroups	= explode(",", $this->consignmentHelper->getConfig("consignment/consignment_status/customer_groups"));
		if($customerId){
			$customer = $this->customerRepository->getById($customerId);
			if( $customer->getGroupId() == $consUserGroup || in_array($customer->getGroupId(), $consUserGroups )){
				return true;
			}
		}
	}
}
