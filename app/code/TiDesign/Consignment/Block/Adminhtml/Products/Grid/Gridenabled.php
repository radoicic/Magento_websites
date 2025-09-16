<?php
namespace TiDesign\Consignment\Block\Adminhtml\Products\Grid;

class Gridenabled extends \Magento\Backend\Block\Widget\Grid\Extended
{
	protected 	$productFactory;
	protected 	$productCollFactory;
	private 	$consignmentHelper;
	private 	$status;
	private 	$visibility;
	protected 	$_storeManager;

	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\TiDesign\Consignment\Helper\Consignment $consignmentHelper,
		\Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Catalog\Model\Product\Visibility $visibility,
		\Magento\Eav\Model\Entity\Attribute\Source\Boolean $boolean,
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
        $this->setId('enabledGrid');
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
		//print_r($dd);
		//die();
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
		if(is_array($disabledProducts) && sizeof($disabledProducts) != 0){
			$collection->addAttributeToFilter('entity_id',['nin'=>$disabledProducts]);
		}


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
		$this->getMassactionBlock()->setFormFieldName('disable_id');
		$this->getMassactionBlock()->addItem(
			'disable_product', [
				'label'=> __('Disable Products'),
				'url'  => $this->getUrl('*/*/disableproducts', ['consignment' => $conId,'_current' => true]),
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
		return $this->getUrl('*/products/gridenabled', array('_current'=>true));

	}


}
