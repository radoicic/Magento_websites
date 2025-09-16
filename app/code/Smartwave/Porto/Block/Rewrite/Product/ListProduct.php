<?php
namespace Smartwave\Porto\Block\Rewrite\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\Element;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Url\Helper\Data;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct {
	protected $_defaultToolbarBlock = Toolbar::class;
	protected $_productCollection;
	protected $_catalogLayer;
	protected $_postDataHelper;
	protected $urlHelper;
	protected $categoryRepository;
	private $logger;
	private $consignmentHelper;
	protected $_registry;

	public function __construct(
		\Magento\Catalog\Block\Product\Context $context,
		\Magento\Framework\Data\Helper\PostHelper $postDataHelper,
		\Magento\Catalog\Model\Layer\Resolver $layerResolver,
		\Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
		\Magento\Framework\Url\Helper\Data $urlHelper,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Framework\Registry $registry,
		\TiDesign\Consignment\Helper\Consignment $consignmentHelper,
        array $data = []
	) {
		$this->_customerSession = $customerSession;
		$this->categoryFactory = $categoryFactory;
		$this->_registry = $registry;
		$this->consignmentHelper = $consignmentHelper;
		parent::__construct(
			$context,
			$postDataHelper,
			$layerResolver,
			$categoryRepository,
			$urlHelper,
			$data
		);
	}

	protected function _getProductCollection()
	{
		if ($this->_productCollection === null) {
			$this->_productCollection = $this->initializeProductCollection();
		}

		return $this->_productCollection;
	}


	/**
	 * Retrieve loaded category collection
	 *
	 * @return AbstractCollection
	 */
	public function getLoadedProductCollection()
	{
		return $this->_getProductCollection();
	}

	 private function initializeProductCollection()
	{


		$layer = $this->getLayer();
		/* @var $layer Layer */
		if ($this->getShowRootCategory()) {
			$this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
		}

		// if this is a product view page
		if ($this->_coreRegistry->registry('product')) {
			// get collection of categories this product is associated with
			$categories = $this->_coreRegistry->registry('product')
				->getCategoryCollection()->setPage(1, 1)
				->load();
			// if the product is associated with any category
			if ($categories->count()) {
				// show products from this category
				$this->setCategoryId(current($categories->getIterator())->getId());
			}
		}

		$origCategory = null;
		if ($this->getCategoryId()) {
			try {
				$category = $this->categoryRepository->get($this->getCategoryId());
			} catch (NoSuchEntityException $e) {
				$category = null;
			}

			if ($category) {
				$origCategory = $layer->getCurrentCategory();
				$layer->setCurrentCategory($category);
			}
		}
		$collection = $layer->getProductCollection();

		$this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

		if ($origCategory) {
			$layer->setCurrentCategory($origCategory);
		}

		$this->_eventManager->dispatch(
			'catalog_block_product_list_collection',
			['collection' => $collection]
		);


	   return $collection;
	}
	public function isConsCat()
	{
		$categoryId = $this->_registry->registry('current_category')->getId();
		$concat =  $this->consignmentHelper->isConsignmentCategory($categoryId);
		return $concat;

	}
	public function getConsSource(){

		$categoryId = $this->_registry->registry('current_category')->getId();
		$consource = $this->consignmentHelper->getConsSource($categoryId);
		return $consource;
	}
	public function getDefSource(){
		$defsource = $this->consignmentHelper->getDefaultSource();
		return $defsource;
	}

	public function getConStock($product,$source){
		$stock = $this->consignmentHelper->getConsStock($product,$source);
		return $stock;
	}
	public function isShowDescription(){
		return $this->consignmentHelper->getConfig("consignment/consignment_status/show_description");
	}

}
