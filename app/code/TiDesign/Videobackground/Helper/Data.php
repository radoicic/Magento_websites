<?php
namespace TiDesign\Videobackground\Helper;

use Magento\Framework\Registry;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_storeManager;
    protected 	$_objectManager;
    private 	$_registry;
    protected 	$_filterProvider;
    private 	$_checkedPurchaseCode;
    private 	$_messageManager;
    protected 	$_configFactory;
	protected	$_videoCollectionFactory;
	private 	$_videoCollection;
	private		$_scopeConfig;
	protected 	$_request;
	protected 	$_cmsPage;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configFactory,
		\TiDesign\Videobackground\Model\ResourceModel\Videolist\CollectionFactory $videoCollectionFactory,
		\Magento\Cms\Model\Page $cmsPage,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\App\RequestInterface $request,
        Registry $registry
    ) {
        $this->_storeManager 			= $storeManager;
        $this->_objectManager 			= $objectManager;
        $this->_filterProvider 			= $filterProvider;
        $this->_registry 				= $registry;
        $this->_messageManager 			= $messageManager;
        $this->_configFactory 			= $configFactory;
		$this->_videoCollectionFactory 	= $videoCollectionFactory;
		$this->_scopeConfig 			= $scopeConfig;
		$this->_request 				= $request;
		$this->_cmsPage 				= $cmsPage;

        parent::__construct($context);
    }

    public function getCurrentStore() {
        return $this->_storeManager->getStore();
    }

    protected function prepareCollection()
    {
		$cid = intval($this->getCurrentStore()->getId());
        $this->_videoCollection = $this->_videoCollectionFactory->create()
			->addFieldToFilter('active', array('eq' => '1'))
			->addFieldToFilter('store_id',
				[
					['finset' => 0],
					['finset' => $cid]
				])
            ->setOrder('id', 'DESC');
    }
    public function getCollection()
    {
		$sonuc = [];
        if (is_null($this->_videoCollection)) {
            $this->prepareCollection();
        }


		$showOnEntire = $this->getShowOnEntire();		// show entire site
		if($showOnEntire->count()>0){
			$qty = $showOnEntire->count();
			$sonuc = [
				'source' 	=> 'showOnEntire',
				'qty'		=> $qty,
				'data'		=> $showOnEntire
				];
			return $sonuc;
		}


		$actionName = $this->_request->getFullActionName();

		if( $actionName == 'cms_index_index'){	// home page
			$staticPages = $this->getStaticPages(1);
			if($staticPages->count()>0){
				$qty = $staticPages->count();
				$sonuc = [
					'source' 	=> 'Static Pages / Home Page',
					'qty'		=> $qty,
					'data'		=> $staticPages
					];
			}else{
				$sonuc = [
					'source' 	=> 'Static Pages / Home Page',
					'qty'		=> 0,
					'data'		=> ""
					];
			}
			return $sonuc;
		}

		if( $actionName == 'firecheckout_index_index'){	// checkout page
			$staticPages = $this->getStaticPages(2);
			if($staticPages->count()>0){
				$qty = $staticPages->count();
				$sonuc = [
					'source' 	=> 'Static Pages / Checkout Page',
					'qty'		=> $qty,
					'data'		=> $staticPages
					];
			}else{
				$sonuc = [
					'source' 	=> 'Static Pages / Checkout Page',
					'qty'		=> 0,
					'data'		=> ""
					];
			}
			return $sonuc;
		}
		if( $actionName == 'checkout_cart_index'){	// cart page
			$staticPages = $this->getStaticPages(3);
			if($staticPages->count()>0){
				$qty = $staticPages->count();
				$sonuc = [
					'source' 	=> 'Static Pages / Cart Page',
					'qty'		=> $qty,
					'data'		=> $staticPages
					];
			}else{
				$sonuc = [
					'source' 	=> 'Static Pages / Cart Page',
					'qty'		=> 0,
					'data'		=> ""
					];
			}
			return $sonuc;
		}

		if( $actionName == 'contact_index_index'){	// contact page
			$staticPages = $this->getStaticPages(4);
			if($staticPages->count()>0){
				$qty = $staticPages->count();
				$sonuc = [
					'source' 	=> 'Static Pages / Contact Page',
					'qty'		=> $qty,
					'data'		=> $staticPages
					];
			}else{
				$sonuc = [
					'source' 	=> 'Static Pages / Contact Page',
					'qty'		=> 0,
					'data'		=> ""
					];
			}
			return $sonuc;
		}

		if( $actionName == 'cms_page_view'){	// dynamic page
			$pageId 		= $this->_cmsPage->getIdentifier();
			$page_name 		= 'Dynamic Pages / '.$this->_cmsPage->getTitle();
			$dynamicPages 	= $this->getDynamicPages($pageId);
			if($dynamicPages->count()>0){
				$qty = $dynamicPages->count();
				$sonuc = [
					'source' 	=> $page_name,
					'qty'		=> $qty,
					'data'		=> $dynamicPages
					];
			}else{
				$sonuc = [
					'source' 	=> 'Dynamic Pages / ',
					'qty'		=> 0,
					'data'		=> ""
					];
			}
			return $sonuc;
		}


		if( $actionName == 'catalog_category_view'){	// category page
			$categoryId 		= $this->_registry->registry('current_category')->getId();
			$categoryVideos 	= $this->getCategoryVideo($categoryId);
			if($categoryVideos->count()>0){
				$qty = $categoryVideos->count();
				$sonuc = [
					'source' 	=> 'Category Page',
					'qty'		=> $qty,
					'data'		=> $categoryVideos
					];
			}else{
				$sonuc = [
					'source' 	=> 'Category Page',
					'qty'		=> 0,
					'data'		=> ""
					];
			}
			return $sonuc;
		}

		if( $actionName == 'catalog_product_view'){		// product page
			$productCategories			= $this->_registry->registry('current_product')->getCategoryIds();
			foreach ($productCategories as $cat) {
				$categoryVideos 	= $this->getCategoryVideo($cat);
				if($categoryVideos->count()>0){
					$qty = $categoryVideos->count();
					$sonuc = [
						'source' 	=> 'Product Page',
						'qty'		=> $qty,
						'data'		=> $categoryVideos
						];
				}
			}
			if (!array_key_exists('qty', $sonuc)) {
				$sonuc = [
					'source' 	=> 'Category Page',
					'qty'		=> 0,
					'data'		=> ""
					];
			}
			return $sonuc;
		}


/*
		Magento_Checkout		checkout_cart_index
		Swissup_Firecheckout	firecheckout_index_index
		Magento_Contact			contact_index_index
		homehage				cms_index_index
*/
 //       return $this->_videoCollection;
    }


/* **********************************************************************************************************
										Show On Entire
********************************************************************************************************** */
	public function getShowOnEntire(){
		$entireCollection = clone $this->_videoCollection;
		$showOnEntire = $entireCollection->addFieldToFilter('show_on_entire', array('eq' => '1'));
		return $showOnEntire;
	}
/* ********************************************************************************************************** */




/* **********************************************************************************************************
										Static Pages
********************************************************************************************************** */
	public function getStaticPages($pageId){
		$staticCollection = clone $this->_videoCollection;
		$staticVideo = $staticCollection->addFieldToFilter(['static_pages'],
				[
					['finset' => $pageId]
				]);
		return $staticVideo;
	}


/* **********************************************************************************************************
										Dynamic Pages
********************************************************************************************************** */
	public function getDynamicPages($pageId){
		$dynanicCollection = clone $this->_videoCollection;
		$dynanicVideo = $dynanicCollection->addFieldToFilter(['page_id'],
				[
					['finset' => $pageId]
				]);
		return $dynanicVideo;
	}

/* **********************************************************************************************************
										Show On Category
********************************************************************************************************** */
	public function getCategoryVideo($categoryId){
		$categoryCollection = clone $this->_videoCollection;
		$categoryVideo = $categoryCollection->addFieldToFilter(['category_id'],
				[
					['finset' => $categoryId]
				]);
		return $categoryVideo;
	}



	public function getVideoId($video){
		preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $video, $matches);
		return $matches[1];
	}

	public function getDebugMode(){
        return $this->_scopeConfig->getValue(
                'videobackground/debug_status/enable_frontend',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
	}
	public function getController(){
		 return $this->_request->getControllerModule();
	}
	public function checkDisabled(){
		$disabledList = $this->_scopeConfig->getValue(
                'videobackground/debug_status/disable_for',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
		$disabledArray = explode(",",$disabledList);
		$controllerName = $this->getController();
		return (in_array($controllerName,$disabledArray))? true : false;

	}

	public function getDisabled(){
		$disabledList = $this->_scopeConfig->getValue(
                'videobackground/debug_status/disable_for',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
		$disabledArray = explode(",",$disabledList);
		return $disabledArray;
	}


	public function getMediaUrl(){
		return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
	}
}
