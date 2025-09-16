<?php
namespace TiDesign\Consignment\Block\Adminhtml\Products\Grid;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\Module\Manager;
use Magento\Store\Model\StoreManagerInterface;
use TiDesign\Consignment\Helper\Consignment;

class Griddisabled extends \Magento\Backend\Block\Widget\Grid\Extended
{
	protected 	$productFactory;
	protected 	$productCollFactory;
	private 	$consignmentHelper;
	private 	$status;
	private 	$visibility;
	protected 	$_storeManager;
    private  $moduleManager;

    //public $disabled_product_count;

    public function __construct(
        Context $context,
        Data $backendHelper,
        Manager $moduleManager,
        ProductFactory $productFactory,
        CollectionFactory $productCollFactory,
		StoreManagerInterface $storeManager,
		Consignment $consignmentHelper,
		Status $status,
        Visibility $visibility,
		Boolean $boolean,
        array $data = []
    ) {
        $this->moduleManager = $moduleManager;
        $this->productFactory = $productFactory;
        $this->productCollFactory = $productCollFactory;
        $this->_storeManager = $storeManager;
		$this->status = $status;
		$this->boolean = $boolean;
		$this->consignmentHelper = $consignmentHelper;
        $this->visibility = $visibility ?: ObjectManager::getInstance()->get(Visibility::class);
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('disabledGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }
	protected function _getCategory(){
		return $this->consignmentHelper->getCategoryId($this->getRequest()->getParam('id'));
	}
	protected function _getDisabled(){
		$dd = explode(',',$this->consignmentHelper->getDisabledProducts($this->getRequest()->getParam('id')));
		return $dd;
	}

    protected function _prepareCollection()
    {
		$store = $this->_getStore();
        $collection = $this->productFactory->create()->getCollection()->addAttributeToSelect(
            '*'
        )->setStore(
            $store
        );

		$categoryId = $this->_getCategory();
		$disabledProducts = $this->_getDisabled();
		if($categoryId){
			$collection->addCategoriesFilter(['in' => $categoryId]);
		}

		$collection->addAttributeToFilter('entity_id',['in'=>$disabledProducts]);

    //    $disabled_product_count = count($collection->getAllIds());



		$source = $this->consignmentHelper->getConsSource($categoryId);

		if ($this->moduleManager->isEnabled('Magento_CatalogInventory')) {
			$collection->joinField(
				'qty',
				'inventory_source_item',
				'quantity',
				'sku=sku',
				'{{table}}.source_code="'.$source.'"',
				'left'
			);
		}
        if ($store->getId()) {
            $collection->setStoreId($store->getId());
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                Store::DEFAULT_STORE_ID
            );
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        } else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }

        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

	protected function _prepareColumns()
	{
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'width' => '50px',
                'index' => 'entity_id',
                'type' => 'number',
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'index' => 'sku',
                'header_css_class' => 'col-sku',
                'column_css_class' => 'col-sku',
            ]
        );
        $this->addColumn(
            'qty',
            [
                'header' => __('Qty'),
                'index' => 'qty',
            ]
        );
         $this->addColumn('visibility',
            [
				'header'=> __('Visibility'),
				'width' => '80px',
				'index' => 'visibility',
				'type'  => 'options',
				'options' => $this->visibility->getOptionArray()
            ]
		);
       $this->addColumn('status',
            [
                'header'=> __('Status'),
                'width' => '80px',
                'index' => 'status',
                'type'  => 'options',
                'options' => $this->status->getOptionArray()
            ]
		);

        $this->addColumn(
            'action',
            [
                'header'    => __('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => [
                    [
                        'caption' => __('Edit'),
                        'url' => array('base' => 'catalog/product/edit'),
						'target'=>'_blank',
						'field' => 'id'
                    ]
                ],
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores'
            ]
        );

		$block = $this->getLayout()->getBlock('grid.bottom.links');
		if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }
		return parent::_prepareColumns();
	}

    protected function _prepareMassaction()
    {
		$conId = $this->getRequest()->getParam('id');

		$this->setMassactionIdField('entity_id');
		$this->getMassactionBlock()->setFormFieldName('enable_id');
		$this->getMassactionBlock()->addItem(
			'enable_product', [
				'label'=> __('Enable Products'),
				'url'  => $this->getUrl('*/*/enableproducts', ['consignment' => $conId,'_current' => true]),
				'confirm' => __('Are you sure?'),
			]
		);
		$this->getMassactionBlock()->addItem(
			'remove_product', [
				'label'=> __('Remove Ctegory & Source'),
				'url'  => $this->getUrl('*/*/removeproducts', ['consignment' => $conId,'_current' => true]),
				'confirm' => __('<span class="showLoader"/><b>Remove Ctegory & Source</b>  Are you sure?'),
			]
		);
		return $this;
	}


	public function getGridUrl()
	{
		return $this->getUrl('*/products/griddisabled', array('_current'=>true));
	}

}
