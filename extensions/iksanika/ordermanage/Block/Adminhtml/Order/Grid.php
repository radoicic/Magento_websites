<?php
/**
 * Iksanika llc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.iksanika.com/products/IKS-LICENSE.txt
 *
 * @category   Iksanika
 * @package    Iksanika_Ordermanage
 * @copyright  Copyright (c) 2015 Iksanika llc. (http://www.iksanika.com)
 * @license    http://www.iksanika.com/products/IKS-LICENSE.txt
 */

namespace Iksanika\Ordermanage\Block\Adminhtml\Order;

use Magento\Store\Model\Store;
use Magento\Search\Model\QueryFactory;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Catalog search data
     *
     * @var \Magento\Search\Model\QueryFactory
     */
    protected $queryFactory = null;
    
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory]
     */
    protected $_setsFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Sales\Model\Service\OrderFactory
     */
    protected $_serviceOrderFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_type;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_status;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;

    /**
     * @var \Iksanika\Productmanage\Model\Product\Attribute\Source\Status
     */
    protected $_availability;

    /**
     * @var \Iksanika\Ordermanage\Helper\Data
     */
    public $_helper;
    
    public $_backendHelper;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;

    protected $_template = 'Iksanika_Ordermanage::widget/grid/extended.phtml';
    
    protected static $columnType = array();
    
    public static $columnSettings = array();
    
    public $_dirCurrency;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Type $type
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $status
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param QueryFactory $queryFactory
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product\Type $type,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Framework\Module\Manager $moduleManager,
        \Iksanika\Ordermanage\Model\Product\Attribute\Source\Status $availability,
        \Iksanika\Ordermanage\Helper\Data $helper,
        \Magento\Tax\Model\TaxClass\Source\Product $taxClassSourceProduct,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $eavEntityOptCollection,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\ResourceModel\Order\Collection $orderCollection,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Sales\Model\Order\Item $orderItemConfig,
        \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection,
        \Magento\Directory\Model\Currency $dirCurrency,
        \Magento\Tax\Helper\Data $helperTax,
        \Magento\Weee\Helper\Data $helperWeee,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        
        array $data = []
    ) {
        $this->_websiteFactory = $websiteFactory;
        $this->_setsFactory = $setsFactory;
        $this->_productFactory = $productFactory;
        $this->_type = $type;
        $this->_status = $status;
        $this->_visibility = $visibility;
        $this->_availability = $availability;
        $this->moduleManager = $moduleManager;
        $this->_taxClassSourceProduct = $taxClassSourceProduct;
        $this->_eavEntityOptCollection = $eavEntityOptCollection;
        $this->_helper = $helper;
        $this->_helper->setScopeConfig($context->getScopeConfig());
        parent::__construct($context, $backendHelper, $data);
        $this->_objectManager = $objectManager;
        $this->_productModel = $productModel;
        $this->_orderCollection = $orderCollection;
        $this->_orderFactory = $orderFactory;
        $this->_orderConfig = $orderConfig;
        $this->_orderItemConfig = $orderItemConfig;
        $this->_countryCollection = $countryCollection;
        $this->_dirCurrency = $dirCurrency;
        $this->_helperTax = $helperTax;
        $this->_helperWeee = $helperWeee;
        $this->_resource = $resource;
        $this->_backendHelper = $backendHelper;
        $this->addressRenderer = $addressRenderer;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        
        $this->prepareDefaults();
        
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setId('orderGrid');
//        self::prepareColumnSettings();
    }
    
    private function prepareDefaults() 
    {
        $this->setDefaultLimit($this->_scopeConfig->getValue('iksanika_ordermanage/columns/limit'));
        $this->setDefaultPage($this->_scopeConfig->getValue('iksanika_ordermanage/columns/page'));
        $this->setDefaultSort($this->_scopeConfig->getValue('iksanika_ordermanage/columns/sort'));
        $this->setDefaultDir($this->_scopeConfig->getValue('iksanika_ordermanage/columns/dir'));
        self::$columnType = $this->_helper->getColumnType();
    }
    
    protected function _prepareLayout()
    {
        $this->setChild('save_config_button',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData(
                [
                    'label'     => __('Save Config'),
                    'onclick'   => $this->getJsObjectName().'.doSaveConfig(\''.($this->getUrl('ordermanage/*/saveConfig', array('_current' => true))).'\')',
                    'class'     => 'primary btnSaveConfig'
                ]
            )
        );
        
        return parent::_prepareLayout();
    }

    public function getSaveConfigButtonHtml()
    {
        return $this->getChildHtml('save_config_button');
    }

    /**
     * @return $this
     */
    protected function _prepareCollectionOrders()
    {
        $store = $this->_helper->getStore();
        $collection = $this->_orderFactory->create()->getCollection();
        $collection->addAttributeToSelect('*')
//            ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
//            ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left');
                ;
        
        $collection->getSelect()->joinLeft(
            array('sog' => $this->_resource->getTableName('sales_order_grid')),
            'main_table.entity_id = sog.entity_id',
            array(
                'sog.shipping_name',
                'sog.billing_name'
            )
        );
        if(
            $this->_helper->colIsVisible('shipping_full_form') || 
            $this->_helper->colIsVisible('shipping_name') || 
            $this->_helper->colIsVisible('shipping_firstname') || 
            $this->_helper->colIsVisible('shipping_lastname') || 
            $this->_helper->colIsVisible('shipping_middlename') || 
            $this->_helper->colIsVisible('shipping_company') || 
            $this->_helper->colIsVisible('shipping_street') || 
            $this->_helper->colIsVisible('shipping_city') || 
            $this->_helper->colIsVisible('shipping_region') || 
            $this->_helper->colIsVisible('shipping_postcode') || 
            $this->_helper->colIsVisible('shipping_email') || 
            $this->_helper->colIsVisible('shipping_telephone') || 
            $this->_helper->colIsVisible('shipping_country') || 
            $this->_helper->colIsVisible('shipping_fax')
            )
        {
            $collection->getSelect()->joinLeft(
                array('soa_shippment' => $this->_resource->getTableName('sales_order_address')), //Mage::getConfig()->getTablePrefix().
                'main_table.entity_id = soa_shippment.parent_id AND soa_shippment.address_type="shipping"',
                array(
//                    'shipping_name'          => new Zend_Db_Expr('CONCAT_WS(\' \', soa_shipping.firstname, soa_shipping.lastname)'),
                    'shipping_full_form'    => new \Zend_Db_Expr('CONCAT_WS(\' \', soa_shippment.firstname, soa_shippment.lastname, soa_shippment.middlename, soa_shippment.company, soa_shippment.street, soa_shippment.city, soa_shippment.region, soa_shippment.postcode, soa_shippment.email, soa_shippment.telephone, soa_shippment.fax)'),
                    'shipping_firstname'    => 'soa_shippment.firstname',
                    'shipping_lastname'     => 'soa_shippment.lastname',
                    'shipping_middlename'   => 'soa_shippment.middlename',
                    'shipping_company'      => 'soa_shippment.company',
                    'shipping_street'       => 'soa_shippment.street',
                    'shipping_city'         => 'soa_shippment.city',
                    'shipping_region'       => 'soa_shippment.region',
                    'shipping_postcode'     => 'soa_shippment.postcode',
                    'shipping_email'        => 'soa_shippment.email',
                    'shipping_telephone'    => 'soa_shippment.telephone',
                    'shipping_country'      => 'soa_shippment.country_id',
                    'shipping_fax'          => 'soa_shippment.fax',
                )
            );
        }
        
        
        if(
            $this->_helper->colIsVisible('billing_full_form') || 
            $this->_helper->colIsVisible('billing_name') || 
            $this->_helper->colIsVisible('billing_firstname') || 
            $this->_helper->colIsVisible('billing_lastname') || 
            $this->_helper->colIsVisible('billing_middlename') || 
            $this->_helper->colIsVisible('billing_company') || 
            $this->_helper->colIsVisible('billing_street') || 
            $this->_helper->colIsVisible('billing_city') || 
            $this->_helper->colIsVisible('billing_region') || 
            $this->_helper->colIsVisible('billing_postcode') || 
            $this->_helper->colIsVisible('billing_email') || 
            $this->_helper->colIsVisible('billing_telephone') || 
            $this->_helper->colIsVisible('billing_country') || 
            $this->_helper->colIsVisible('billing_fax')
            )
        {
            $collection->getSelect()->joinLeft(
                array('soa_billing' => $this->_resource->getTableName('sales_order_address')), //Mage::getConfig()->getTablePrefix().
                'main_table.entity_id = soa_billing.parent_id AND soa_billing.address_type="billing"',
                array(
//                    'billing_name'          => new Zend_Db_Expr('CONCAT_WS(\' \', soa_billing.firstname, soa_billing.lastname)'),
                    'billing_full_form'     => new \Zend_Db_Expr('CONCAT_WS(\' \', soa_billing.firstname, soa_billing.lastname, soa_billing.middlename, soa_billing.company, soa_billing.street, soa_billing.city, soa_billing.region, soa_billing.postcode, soa_billing.email, soa_billing.telephone, soa_billing.fax)'),
                    'billing_firstname'     => 'soa_billing.firstname',
                    'billing_lastname'      => 'soa_billing.lastname',
                    'billing_middlename'    => 'soa_billing.middlename',
                    'billing_company'       => 'soa_billing.company',
                    'billing_street'        => 'soa_billing.street',
                    'billing_city'          => 'soa_billing.city',
                    'billing_region'        => 'soa_billing.region',
                    'billing_postcode'      => 'soa_billing.postcode',
                    'billing_email'         => 'soa_billing.email',
                    'billing_telephone'     => 'soa_billing.telephone',
                    'billing_country'       => 'soa_billing.country_id',
                    'billing_fax'           => 'soa_billing.fax',
                )
            );
        }

        
        //
        if($this->_scopeConfig->getValue('iksanika_ordermanage/products/includeproducts') || $this->_helper->colIsVisible('name') || $this->_helper->colIsVisible('sku'))
        {
            $collection->join(
                'sales_order_item', 
                '`sales_order_item`.order_id=main_table.entity_id', 
                array(
                    'name' => new \Zend_Db_Expr('group_concat(distinct `sales_order_item`.name SEPARATOR ", ")'),
                    'sku' => new \Zend_Db_Expr('group_concat(distinct `sales_order_item`.sku SEPARATOR ", ")'),
    //                    'qty_ordered'=>'qty_ordered',
                ),
                'GROUP BY `sales_order_item`.sku',
//                null,
                'left'
            );
            $collection->getSelect()->group('main_table.entity_id');
        }
        
        if($this->_scopeConfig->getValue('iksanika_ordermanage/columns/hide_status'))
        {
            $excludeStatuses = explode(',', $this->_scopeConfig->getValue('iksanika_ordermanage/columns/hide_status'));
            $collection->addAttributeToFilter('main_table.status', array('nin' => $excludeStatuses));
        }
        
//$collection->printLogQuery(true);
//        echo get_class($collection);
//        die();
        $this->setCollection($collection);
        
//        return \Magento\Backend\Block\Widget\Grid\Extended::_prepareCollection();
        return parent::_prepareCollection();
    }
    

    /**
     * @return $this
     */
    protected function _prepareCollectionOrderedItems()
    {
        $store = $this->_helper->getStore();
//        $collection = $this->_orderFactory->create()->getCollection();
        $collection = $this->_objectManager->create('Magento\Sales\Model\ResourceModel\Order\Item\Collection');
        $collection->addAttributeToSelect('*');

        // join order information collection
        $collection->getSelect()->join(
            'sales_order',
            'main_table.order_id = sales_order.entity_id',
            '*'//array('telephone', 'city', 'postcode', 'country_id')
        );//->where("sales_flat_order_address.address_type = 'billing'");        
        
        
        $collection->getSelect()->joinLeft(
            array('sog' => $this->_resource->getTableName('sales_order_grid')),
//            'main_table.order_id = sog.entity_id',
            'main_table.order_id = sog.entity_id',
            array(
                'sog.shipping_name',
                'sog.billing_name'
            )
        );
        if(
            $this->_helper->colIsVisible('shipping_full_form') || 
            $this->_helper->colIsVisible('shipping_name') || 
            $this->_helper->colIsVisible('shipping_firstname') || 
            $this->_helper->colIsVisible('shipping_lastname') || 
            $this->_helper->colIsVisible('shipping_middlename') || 
            $this->_helper->colIsVisible('shipping_company') || 
            $this->_helper->colIsVisible('shipping_street') || 
            $this->_helper->colIsVisible('shipping_city') || 
            $this->_helper->colIsVisible('shipping_region') || 
            $this->_helper->colIsVisible('shipping_postcode') || 
            $this->_helper->colIsVisible('shipping_email') || 
            $this->_helper->colIsVisible('shipping_telephone') || 
            $this->_helper->colIsVisible('shipping_country') || 
            $this->_helper->colIsVisible('shipping_fax')
            )
        {
            $collection->getSelect()->joinLeft(
                array('soa_shippment' => $this->_resource->getTableName('sales_order_address')), //Mage::getConfig()->getTablePrefix().
//                'main_table.entity_id = soa_shippment.parent_id AND soa_shippment.address_type="shipping"',
                'main_table.order_id = soa_shippment.parent_id AND soa_shippment.address_type="shipping"',
                array(
//                    'shipping_name'          => new Zend_Db_Expr('CONCAT_WS(\' \', soa_shipping.firstname, soa_shipping.lastname)'),
                    'shipping_full_form'    => new \Zend_Db_Expr('CONCAT_WS(\' \', soa_shippment.firstname, soa_shippment.lastname, soa_shippment.middlename, soa_shippment.company, soa_shippment.street, soa_shippment.city, soa_shippment.region, soa_shippment.postcode, soa_shippment.email, soa_shippment.telephone, soa_shippment.fax)'),
                    'shipping_firstname'    => 'soa_shippment.firstname',
                    'shipping_lastname'     => 'soa_shippment.lastname',
                    'shipping_middlename'   => 'soa_shippment.middlename',
                    'shipping_company'      => 'soa_shippment.company',
                    'shipping_street'       => 'soa_shippment.street',
                    'shipping_city'         => 'soa_shippment.city',
                    'shipping_region'       => 'soa_shippment.region',
                    'shipping_postcode'     => 'soa_shippment.postcode',
                    'shipping_email'        => 'soa_shippment.email',
                    'shipping_telephone'    => 'soa_shippment.telephone',
                    'shipping_country'      => 'soa_shippment.country_id',
                    'shipping_fax'          => 'soa_shippment.fax',
                )
            );
        }
        
        
        if(
            $this->_helper->colIsVisible('billing_full_form') || 
            $this->_helper->colIsVisible('billing_name') || 
            $this->_helper->colIsVisible('billing_firstname') || 
            $this->_helper->colIsVisible('billing_lastname') || 
            $this->_helper->colIsVisible('billing_middlename') || 
            $this->_helper->colIsVisible('billing_company') || 
            $this->_helper->colIsVisible('billing_street') || 
            $this->_helper->colIsVisible('billing_city') || 
            $this->_helper->colIsVisible('billing_region') || 
            $this->_helper->colIsVisible('billing_postcode') || 
            $this->_helper->colIsVisible('billing_email') || 
            $this->_helper->colIsVisible('billing_telephone') || 
            $this->_helper->colIsVisible('billing_country') || 
            $this->_helper->colIsVisible('billing_fax')
            )
        {
            $collection->getSelect()->joinLeft(
                array('soa_billing' => $this->_resource->getTableName('sales_order_address')), //Mage::getConfig()->getTablePrefix().
//                'main_table.entity_id = soa_billing.parent_id AND soa_billing.address_type="billing"',
                'main_table.order_id = soa_billing.parent_id AND soa_billing.address_type="billing"',
                array(
//                    'billing_name'          => new Zend_Db_Expr('CONCAT_WS(\' \', soa_billing.firstname, soa_billing.lastname)'),
                    'billing_full_form'     => new \Zend_Db_Expr('CONCAT_WS(\' \', soa_billing.firstname, soa_billing.lastname, soa_billing.middlename, soa_billing.company, soa_billing.street, soa_billing.city, soa_billing.region, soa_billing.postcode, soa_billing.email, soa_billing.telephone, soa_billing.fax)'),
                    'billing_firstname'     => 'soa_billing.firstname',
                    'billing_lastname'      => 'soa_billing.lastname',
                    'billing_middlename'    => 'soa_billing.middlename',
                    'billing_company'       => 'soa_billing.company',
                    'billing_street'        => 'soa_billing.street',
                    'billing_city'          => 'soa_billing.city',
                    'billing_region'        => 'soa_billing.region',
                    'billing_postcode'      => 'soa_billing.postcode',
                    'billing_email'         => 'soa_billing.email',
                    'billing_telephone'     => 'soa_billing.telephone',
                    'billing_country'       => 'soa_billing.country_id',
                    'billing_fax'           => 'soa_billing.fax',
                )
            );
        }
        
        if($this->_scopeConfig->getValue('iksanika_ordermanage/columns/hide_status'))
        {
            $excludeStatuses = explode(',', $this->_scopeConfig->getValue('iksanika_ordermanage/columns/hide_status'));
            $collection->addAttributeToFilter('sales_order.status', array('nin' => $excludeStatuses));
        }
//$collection->printLogQuery(true);die();
//        echo get_class($collection);
//        die();
        $this->setCollection($collection);
        
//        return \Magento\Backend\Block\Widget\Grid\Extended::_prepareCollection();
        return parent::_prepareCollection();
    }
    
    protected function _prepareCollection()
    {
        if($this->_isExport == \Iksanika\Ordermanage\Model\Export::ORDERED_ITEMS)
        {
            $this->_prepareCollectionOrderedItems();
        }else
        {
            $this->_prepareCollectionOrders();
        }
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField(
                    'websites',
                    'catalog_product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left'
                );
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }
    
    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumnsOrders()
    {
        $store = $this->_helper->getStore();
        $orderTable = 'main_table.';
        if($this->_isExport == \Iksanika\Ordermanage\Model\Export::ORDERED_ITEMS)
        {
            $orderTable = 'sales_order.';
        }
        
        if($this->_helper->colIsVisible('increment_id'))
        {
            $this->addColumn(
                'increment_id',
                [
                    'header' => __('Order #'),
                    'type' => 'text',
                    'index' => 'increment_id',
                    'header_css_class' => 'col-id',
                    'column_css_class' => 'col-id',
                    
                    'filter_index' => $orderTable.'increment_id',
                ]
            );
        }
        
        if($this->_helper->colIsVisible('store_id')) 
        {
            $this->addColumn('store_id', 
                [
                    'header'    => __('Purchase Point'),
                    'index'     => 'store_id',
                    'id' => 'store_id',
                    'filter_index' => $orderTable.'store_id',
                    'type'      => 'store',
                    'store_view'=> true,
                    'display_deleted' => true,
                    'header_css_class' => 'col-from-store',
                    'column_css_class' => 'col-from-store',
                ]
            );
        }
        
        if($this->_helper->colIsVisible('created_at')) 
        {
            $this->addColumn(
                'created_at',
                [
                    'header' => __('Purchased On'),
                    'index' => 'created_at',
                    'type' => 'datetime',
                    'width' => '100px',
                    'filter_index' => $orderTable.'created_at'
                ]
            );
        }
        
        if($this->_helper->colIsVisible('billing_name')) 
        {
            $this->addColumn(
                'billing_name', [
                    'header'        =>  __('Bill-to Name'),
                    'index'         =>  'billing_name',
                    'filter_index'  =>  'sog.billing_name',
                ]
            );
        }
        
        if($this->_helper->colIsVisible('shipping_name')) 
        {
            $this->addColumn(
                'shipping_name', [
                    'header'        =>  __('Ship-to Name'),
                    'index'         =>  'shipping_name',
                    'filter_index'  =>  'sog.shipping_name',
                ]
            );
        }
        
        if($this->_helper->colIsVisible('base_grand_total')) 
        {
            $this->addColumn(
                'base_grand_total', [
                    'header' => __('Grand Total (Base)'),
                    'index' => 'base_grand_total',
                    'filter_index' => $orderTable.'base_grand_total',
                    'type'  => 'currency',
                    'currency' => 'base_currency_code',
                ]
            );
        }
        
        if($this->_helper->colIsVisible('grand_total')) 
        {
            $this->addColumn(
                'grand_total', [
                    'header' => __('Grand Total (Purchased)'),
                    'index' => 'grand_total',
                    'filter_index' => $orderTable.'grand_total',
                    'type'  => 'currency',
                    'currency' => 'order_currency_code',
                ]
            );
        }
        
        if($this->_helper->colIsVisible('status')) 
        {
            $this->addColumn('status',
                [
                    'header' => __('Status'),
                    'index' => 'status',
                    'filter_index' => $orderTable.'status',
                    //'type'  => 'options',
                    'type'  => 'options',
                    'width' => '70px',
//                    'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
                    'options' => $this->_orderConfig->getStatuses(),
                    'renderer' => 'Iksanika\Ordermanage\Block\Widget\Grid\Column\Renderer\OrderStatus',
                ]
            );
        }
        
        $ignoreCols = [
            'increment_id'      =>  true,
            'store_id'          =>  true,
            'created_at'        =>  true,
            'billing_name'      =>  true,
            'shipping_name'     =>  true,
            'base_grand_total'  =>  true,
            'grand_total'       =>  true,
            'status'            =>  true,
        ];
        
        $defaults = [
            'remote_ip' => [
                'header' => __('Remote IP'),
                'index' => 'remote_ip',
                'type'  => 'ip',
            ],
        ];
        
        
        
        foreach($this->_helper->getColumnSettings() as $col => $true)
        {
            if(isset($ignoreCols[$col])) 
                continue;
            
            if(isset($defaults[$col])) 
            {
                $innerSettings = $defaults[$col];
            } else 
            if(isset(self::$columnType[$col]))
            {
                
                $innerSettings = [
                    'header'=> (isset(self::$columnType[$col]['title'])) ? __(self::$columnType[$col]['title']) : __($col),
                    'width' => '100px',
//                    'type'  => self::$columnType[$col]['type'],
                    'type'  => self::$columnType[$col]['type'] != 'country' ? self::$columnType[$col]['type'] : 'select',
                ];
                
                if(isset(self::$columnType[$col]['filter_index']))
                {
                    $innerSettings['filter_index'] = self::$columnType[$col]['filter_index'];
                }
                
                
                if(self::$columnType[$col]['type'] == 'country')
                {
//                    $innerSettings['filter'] = 'Iksanika\Ordermanage\Model\System\Config\Source\Columns\Country';
                    $innerSettings['filter'] = 'Iksanika\Ordermanage\Model\System\Config\Source\Columns\Country';
//                    $innerSettings['renderer'] = 'Iksanika\Ordermanage\Model\System\Config\Source\Columns\Country';
                    
                    $countriesList = array();
                    $countries = $this->_countryCollection->loadByStore()->toOptionArray();
                    foreach($countries as $country)
                    {
                        $countriesList[$country['value']] = $country['label'];
                    }
                    $innerSettings['options'] = $countriesList;
                }
            } else 
            {
                $innerSettings = [
//                    'header'=> Mage::helper('catalog')->__($col),
                    'header'=> __(ucwords(str_replace('_', ' ', $col))),
                    'width' => '100px',
                    'type'  => 'text',
                    
                    // enhacement for Franco comcast
//                    'type'  => 'input',
                ];
                
                if($col == "shipping_tracking_number")
                {
                    $innerSettings['renderer'] = 'Iksanika\Ordermanage\Block\Widget\Grid\Column\Renderer\Shippment';
                    $innerSettings['sortable'] = false;
                }
                
            }
            $innerSettings['index'] = $col;
            $innerSettings['filter_index'] = isset($innerSettings['filter_index']) ? $innerSettings['filter_index'] : $orderTable.''.$col;
            
            // @TODO: remove this part - start, add to prepareCollection conditions to include it from DB query, and remove call from grid.phtml
            if($col == 'payment_method' || $col == 'payment_code' || $col == 'payment_cc_type' || $col == 'special_qty_uniq_prods' ||
                $col == 'shipping_tracking_number')
            {
                $innerSettings['filter']    =   false;
                $innerSettings['sortable']  =   false;
            }
            // @TODO: remove this part - end
            
            
            $this->addColumn($col, $innerSettings);
        }
        
        
        
        
        
        if($this->_isExport == \Iksanika\Ordermanage\Model\Export::NO_EXPORT)
        {
            $this->addColumn(
                'view', 
                [
                    'header' => __('Action'),
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => [
                        [
                            'caption' => __('View'),
                            'url' => [
                                'base' => 'sales/order/view',
                                'params' => ['store' => $this->getRequest()->getParam('store')]
                            ],
                            'field' => 'order_id'
                        ]
                    ],
                    'filter' => false,
                    'sortable' => false,
                    'index' => 'link_view',
                ]
            );
            
            $this->addExportType('ordermanage/*/exportCsv', __('CSV'));
            $this->addExportType('ordermanage/*/exportXml', __('XML'));
            $this->addExportType('ordermanage/*/exportCsvItems', __('CSV (Items)'));
            //$this->addExportType('ordermanage/*/exportXmlItems', __('XML (Items)'));
        }
        
        if($this->_isExport != \Iksanika\Ordermanage\Model\Export::ORDERED_ITEMS)
        {
            return parent::_prepareColumns();
        }
    }
    
    protected function _prepareColumnsOrderedItems()
    {
        $orderTable = 'sales_order.';
        if($this->_isExport == \Iksanika\Ordermanage\Model\Export::ORDERED_ITEMS)
        {
            $orderTable = 'main_table.';
        }

        $this->_prepareColumnsOrders();
        
        $productsAttributes = $this->_scopeConfig->getValue('iksanika_ordermanage/products/showattr');
        $productsAttributes = explode(',',$productsAttributes);
        $productsAttributes = array_flip($productsAttributes);
        
        $productsAttr = array();
        foreach(\Iksanika\Ordermanage\Model\System\Config\Source\Columns\Products::$columnsTitleProducts as $indexKey => $value)
        {
            if($indexKey != 'be_link' && $indexKey != 'fe_link')
            {
                $productsAttr[$indexKey] = (isset($productsAttributes[$indexKey])) ? true : false;
            }
        }

        foreach($productsAttr as $index => $title)
        {
            $this->addColumn(
                'ordered_item_'.$index,
                [
                    'header' => __(\Iksanika\Ordermanage\Model\System\Config\Source\Columns\Products::$columnsTitleProducts[$index]),
                    'index' =>  $index,
                    'filter_index' => $orderTable.$index,
                ]
            );
        }


/*        echo '<pre>';
        var_dump($this->_helper->getColumnSettings());
        echo '</pre>';
die();
 */
/*
        if($this->_helper->colIsVisible('store_id')) 
        {
            $this->addColumn('store_id', 
                [
                    'header'    => __('Purchase Point'),
                    'index'     => 'store_id',
                    'id' => 'store_id',
                    'filter_index' => 'main_table.store_id',
                    'type'      => 'store',
                    'store_view'=> true,
                    'display_deleted' => true,
                    'header_css_class' => 'col-from-store',
                    'column_css_class' => 'col-from-store',
                ]
            );
        }
        
        if($this->_helper->colIsVisible('created_at')) 
        {
            $this->addColumn(
                'created_at',
                [
                    'header' => __('Purchased On'),
                    'index' => 'created_at',
                    'type' => 'datetime',
                    'width' => '100px',
                    'filter_index' => 'main_table.created_at'
                ]
            );
        }

        if($this->_helper->colIsVisible('billing_name')) 
        {
            $this->addColumn(
                'billing_name', [
                    'header'        =>  __('Bill-to Name'),
                    'index'         =>  'billing_name',
                    'filter_index'  =>  'sog.billing_name',
                ]
            );
        }

        if($this->_helper->colIsVisible('shipping_name')) 
        {
            $this->addColumn(
                'shipping_name', [
                    'header'        =>  __('Ship-to Name'),
                    'index'         =>  'shipping_name',
                    'filter_index'  =>  'sog.shipping_name',
                ]
            );
        }
        
        if($this->_helper->colIsVisible('base_grand_total')) 
        {
            $this->addColumn(
                'base_grand_total', [
                    'header' => __('Grand Total (Base)'),
                    'index' => 'base_grand_total',
                    'filter_index' => 'main_table.base_grand_total',
                    'type'  => 'currency',
                    'currency' => 'base_currency_code',
                ]
            );
        }
        
        if($this->_helper->colIsVisible('grand_total')) 
        {
            $this->addColumn(
                'grand_total', [
                    'header' => __('Grand Total (Purchased)'),
                    'index' => 'grand_total',
                    'filter_index' => 'main_table.grand_total',
                    'type'  => 'currency',
                    'currency' => 'order_currency_code',
                ]
            );
        }

        if($this->_helper->colIsVisible('status')) 
        {
            $this->addColumn('status', 
                [
                    'header' => __('Status'),
                    'index' => 'status',
                    'filter_index' => 'main_table.status',
                    //'type'  => 'options',
                    'type'  => 'options',
                    'width' => '70px',
//                    'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
                    'options' => $this->_orderConfig->getStatuses(),
                    'renderer' => 'Iksanika\Ordermanage\Block\Widget\Grid\Column\Renderer\OrderStatus',
                ]
            );
        }
*/        
        return parent::_prepareColumns();
    }
   
    protected function _prepareColumns()
    {
        if($this->_isExport == \Iksanika\Ordermanage\Model\Export::ORDERED_ITEMS)
        {
            $this->_prepareColumnsOrderedItems();
        }else
        {
            $this->_prepareColumnsOrders();
        }
    }
    
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $_auth = $this->_authorization;
        if($_auth->isAllowed('Iksanika_Ordermanage::actions'))
        {
            $this->setMassactionIdField('entity_id');
            $this->getMassactionBlock()->setFormFieldName('order_ids');
    //        $this->getMassactionBlock()->setUseSelectAll(false);

            if($_auth->isAllowed('Iksanika_Ordermanage::ma_cancel'))
            {
                $this->getMassactionBlock()->addItem(
                    'cancel_order', [
                        'label'=> __('Cancel'),
                        'url'  => $this->getUrl('ordermanage/*/defaultMassCancel'),
                    ]
                );
            }
            if($_auth->isAllowed('Iksanika_Ordermanage::ma_hold'))
            {
                $this->getMassactionBlock()->addItem(
                    'hold_order', [
                        'label'=> __('Hold'),
                        'url'  => $this->getUrl('ordermanage/*/defaultMassHold'),
                    ]
                );
            }
            if($_auth->isAllowed('Iksanika_Ordermanage::ma_unhold'))
            {
                $this->getMassactionBlock()->addItem(
                    'unhold_order', [
                        'label'=> __('Unhold'),
                        'url'  => $this->getUrl('ordermanage/*/defaultMassUnhold'),
                    ]
                );
            }
            if($_auth->isAllowed('Iksanika_Ordermanage::ma_print_invoices'))
            {
                $this->getMassactionBlock()->addItem(
                    'pdfinvoices_order', [
                        'label'=> __('Print Invoices'),
                        'url'  => $this->getUrl('ordermanage/*/defaultMassPdfinvoices'),
                    ]
                );
            }
            if($_auth->isAllowed('Iksanika_Ordermanage::ma_print_packing_slips'))
            {
                $this->getMassactionBlock()->addItem(
                    'pdfshipments_order', [
                        'label'=> __('Print Packing Slips'),
                        'url'  => $this->getUrl('ordermanage/*/defaultMassPdfshipments'),
                    ]
                );
            }
            if($_auth->isAllowed('Iksanika_Ordermanage::ma_print_credit_memos'))
            {
                $this->getMassactionBlock()->addItem(
                    'pdfcreditmemos_order', [
                        'label'=> __('Print Credit Memos'),
                        'url'  => $this->getUrl('ordermanage/*/defaultMassPdfcreditmemos'),
                    ]
                );
            }
            if($_auth->isAllowed('Iksanika_Ordermanage::ma_print_all'))
            {
                $this->getMassactionBlock()->addItem(
                    'pdfdocs_order', [
                        'label'=> __('Print All'),
                        'url'  => $this->getUrl('ordermanage/*/defaultMassPdfdocs'),
                    ]
                );
            }
            if($_auth->isAllowed('Iksanika_Ordermanage::ma_print_shipping_lables'))
            {
                $this->getMassactionBlock()->addItem(
                    'print_shipping_label', [
                        'label'=> __('Print Shipping Labels'),
                        'url'  => $this->getUrl('ordermanage/*/defaultMassPrintShippingLabel'),
                    ]
                );
            }





            if(
                $_auth->isAllowed('Iksanika_Ordermanage::ma_update') ||
                $_auth->isAllowed('Iksanika_Ordermanage::ma_setstatus') ||

                $_auth->isAllowed('Iksanika_Ordermanage::ma_invoice') ||
                $_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_capture') ||
                $_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_capture_ship') ||
                $_auth->isAllowed('Iksanika_Ordermanage::ma_capture') ||
                $_auth->isAllowed('Iksanika_Ordermanage::ma_ship') ||
                $_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_print') ||
                $_auth->isAllowed('Iksanika_Ordermanage::ma_ship_print') ||
                $_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_capture_ship_print') ||
                $_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_capture_ship_complete') ||
                $_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_capture_ship_complete_print') ||
                $_auth->isAllowed('Iksanika_Ordermanage::ma_sendorderemail') ||
                $_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_sendemail') ||
                $_auth->isAllowed('Iksanika_Ordermanage::ma_ship_sendemail') ||
                $_auth->isAllowed('Iksanika_Ordermanage::ma_complete') ||
                $_auth->isAllowed('Iksanika_Ordermanage::ma_setprocessing') ||
                $_auth->isAllowed('Iksanika_Ordermanage::ma_delete')
            )
            {

                /*
                 * Prepare list of columns for update
                 */
                $this->getMassactionBlock()->addItem('otherDivider', $this->getSubDivider("------Additional------"));
                $fields = $this->_helper->getColumnForUpdate();

                if(
                    $_auth->isAllowed('Iksanika_Ordermanage::ma_update') ||
                    $_auth->isAllowed('Iksanika_Ordermanage::ma_setstatus')
                )
                {
                    $this->getMassactionBlock()->addItem('otherDivider3-1', $this->getSubDivider('', true));
                    // +Save Changes
                    $this->getMassactionBlock()->addItem('save',
                        array(
                            'label' => __('Save Changes'),
                            'url'   => $this->getUrl('ordermanage/*/massUpdateOrders', array('_current'=>true)),
                            'fields' => $fields
                        )
                    );
                    // + Change Order Status
                    $this->getMassactionBlock()->addItem('changeOrderStatus',
                        array(
                            'label' => __('Change Order Status'),
                            'url'   => $this->getUrl('ordermanage/*/massRunAction', array('_current'=>true, 'trigger_actions' => 'setstatus')),
                            'fields' => $fields,
                            'additional' => [
                                'new-order-status' => [
                                    'name' => 'new-order-status',
                                    'type' => 'select',
                                    'class' => 'required-entry',
                                    'label' => __('Status: '),
                                    'values' => $this->_orderConfig->getStatuses()
                                ]
                            ]
                        )
                    );
                    $this->getMassactionBlock()->addItem('otherDivider3-2', $this->getSubDivider('', true));
                }

                $fields4Update = $this->_helper->getColumnSettings();
                $isShippingTrackingNumber = false;

                foreach($fields4Update as $attributeCode => $attributeStatus)
                {
                    if($attributeCode == 'shipping_tracking_number')
                    {
                        $isShippingTrackingNumber = true;
                    }
                }

                $fields4Update = array('order_ids');
                if($isShippingTrackingNumber)
                {
                    $fields4Update[] = 'shipping_tracking_number';
                    $fields4Update[] = 'shipping_tracking_number_carrier';
                }

                $runMACapture   = $this->_scopeConfig->isSetFlag('iksanika_ordermanage/capture/captureInMassAction');
                $maCaptureTitle = $runMACapture ? (' » '.__('Capture')) : '';
                $maCaptureAction= $runMACapture ? '-capture' : '';


                if(
                    $_auth->isAllowed('Iksanika_Ordermanage::ma_invoice') ||
                    $_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_capture') ||
                    $_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_capture_ship') ||
                    $_auth->isAllowed('Iksanika_Ordermanage::ma_capture') ||
                    $_auth->isAllowed('Iksanika_Ordermanage::ma_ship') ||
                    $_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_print') ||
                    $_auth->isAllowed('Iksanika_Ordermanage::ma_ship_print') ||
                    $_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_capture_ship_print') ||
                    $_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_capture_ship_complete') ||
                    $_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_capture_ship_complete_print')
                ) {


                    $this->getMassactionBlock()->addItem('otherDivider-withNotification', $this->getSubDivider(" Notify Customer "));

                    // + Invoice / Notify
                    if ($_auth->isAllowed('Iksanika_Ordermanage::ma_invoice'))
                    {
                        $this->getMassactionBlock()->addItem('massInvoiceNotify',
                            array(
                                'label' => __('Invoice'),
                                //'url'   => $this->getUrl('ordermanage/*/massInvoiceCapture', array('_current'=>true, 'proceedInvoice' => true)),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'invoice-notify')),
                                'fields' => array('order_ids')
                            )
                        );
                    }

                    // + Invoice / Print / Notify
                    if ($_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_print'))
                    {
                        $this->getMassactionBlock()->addItem('massInvoicePrintNotify',
                            array(
                                'label' => __('Invoice') . ' » ' . __('Print'),
                                //'url'   => $this->getUrl('ordermanage/*/massInvoiceCapture', array('_current'=>true, 'proceedInvoice' => true)),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'invoice-print-notify')),
                                'fields' => array('order_ids')
                            )
                        );
                    }

                    // + Capture Payment / Notify
                    if ($_auth->isAllowed('Iksanika_Ordermanage::ma_capture'))
                    {
                        $this->getMassactionBlock()->addItem('massCaptureNotify',
                            array(
                                'label' => __('Capture'),
                                //'url'   => $this->getUrl('ordermanage/*/massInvoiceCapture', array('_current'=>true, 'proceedCapture' => true)),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'capture')),
                                'fields' => array('order_ids')
                            )
                        );
                    }

                    // + Ship / Notify
                    if ($_auth->isAllowed('Iksanika_Ordermanage::ma_ship'))
                    {
                        $this->getMassactionBlock()->addItem('saveShipNotify',
                            array(
                                'label' => __('Ship'),
                                //'url'   => $this->getUrl('ordermanage/*/massInvoiceCapture', array('_current'=>true, 'proceedShipment' => true)),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'ship-notify')),
                                'fields' => $fields4Update,
                            )
                        );
                    }

                    // + Ship / Print / Notify
                    if ($_auth->isAllowed('Iksanika_Ordermanage::ma_ship_print'))
                    {
                        $this->getMassactionBlock()->addItem('saveShipPrintNotify',
                            array(
                                'label' => __('Ship') . ' » ' . __('Print'),
                                //'url'   => $this->getUrl('ordermanage/*/massInvoiceCapture', array('_current'=>true, 'proceedShipment' => true)),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'ship-print-notify')),
                                'fields' => $fields4Update,
                            )
                        );
                    }

                    /// + Invoice - Capture - Notify
                    if ($_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_capture'))
                    {
                        $this->getMassactionBlock()->addItem('massInvoiceCaptureNotify',
                            array(
                                'label' => __('Invoice') . ' » ' . __('Capture'),
                                //'url'   => $this->getUrl('ordermanage/*/massInvoiceCapture', array('_current'=>true, 'proceedInvoice' => true, 'proceedCapture' => true)),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'invoice-capture-notify')),
                                'fields' => array('order_ids')
                            )
                        );
                    }

                    // + Invoice / Ship
                    if ($_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_capture_ship'))
                    {
                        $this->getMassactionBlock()->addItem('massInvoiceCaptureShipNotify',
                            array(
                                'label' => __('Invoice') . $maCaptureTitle . ' » ' . __('Ship'),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'invoice' . $maCaptureAction . '-ship-notify')),
                                'fields' => $fields4Update,
                            )
                        );
                    }

                    // + Invoice / Ship / Print / Notify
                    if($_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_capture_ship_print'))
                    {
                        $this->getMassactionBlock()->addItem('massInvoiceCaptureShipPrintNotify',
                            array(
                                'label' => __('Invoice') . $maCaptureTitle . ' » ' . __('Ship') . ' » ' . __('Print'),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'invoice' . $maCaptureAction . '-ship-print-notify')),
                                'fields' => $fields4Update,
                            )
                        );
                    }

                    // + Invoice / Ship / Complete / Notify
                    if($_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_capture_ship_complete'))
                    {
                        $this->getMassactionBlock()->addItem('massInvoiceCaptureShipCompleteNotify',
                            array(
                                'label' => __('Invoice') . $maCaptureTitle . ' » ' . __('Ship') . ' » ' . __('Complete'),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'invoice' . $maCaptureAction . '-ship-complete-notify')),
                                'fields' => $fields4Update,
                            )
                        );
                    }

                    // + Invoice / Ship / Complete / Print / Notify
                    if($_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_capture_ship_complete_print'))
                    {
                        $this->getMassactionBlock()->addItem('massInvoiceCaptureShipCompletePrintNotify',
                            array(
                                'label' => __('Invoice') . $maCaptureTitle . ' » ' . __('Ship') . ' » ' . __('Complete') . ' » ' . __('Print'),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'invoice' . $maCaptureAction . '-ship-complete-print-notify')),
                                'fields' => $fields4Update,
                            )
                        );
                    }


                    $this->getMassactionBlock()->addItem('otherDivider3-3', $this->getSubDivider('', true));

                    // Send Order email
                    if ($_auth->isAllowed('Iksanika_Ordermanage::ma_sendemail_order')) {
                        $this->getMassactionBlock()->addItem('massSendEmailOrder',
                            array(
                                'label' => __('Re-send Order email'),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'sendorderemail')),
                                'fields' => array('order_ids')
                            )
                        );
                    }

                    // Send Invoice email
                    if ($_auth->isAllowed('Iksanika_Ordermanage::ma_sendemail_invoice')) {
                        $this->getMassactionBlock()->addItem('massSendEmailInvoice',
                            array(
                                'label' => __('Re-send Invoice email'),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'invoice-sendemail')),
                                'fields' => array('order_ids')
                            )
                        );
                    }

                    // + Send Invoice email
                    if ($_auth->isAllowed('Iksanika_Ordermanage::ma_sendemail_shipment'))
                    {
                        $this->getMassactionBlock()->addItem('massSendEmailShipment',
                            array(
                                'label' => __('Re-send Shipment email'),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'ship-sendemail')),
                                'fields' => array('order_ids')
                            )
                        );
                    }

                    $this->getMassactionBlock()->addItem('otherDivider3-5', $this->getSubDivider('', true));


                    $this->getMassactionBlock()->addItem('otherDivider-noNotification', $this->getSubDivider(" Don't Notify Customer "));


                    // + Invoice
                    if ($_auth->isAllowed('Iksanika_Ordermanage::ma_invoice'))
                    {
                        $this->getMassactionBlock()->addItem('massInvoice',
                            array(
                                'label' => __('Invoice'),
                                //'url'   => $this->getUrl('ordermanage/*/massInvoiceCapture', array('_current'=>true, 'proceedInvoice' => true)),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'invoice')),
                                'fields' => array('order_ids')
                            )
                        );
                    }

                    // + Invoice / Print
                    if ($_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_print'))
                    {
                        $this->getMassactionBlock()->addItem('massInvoicePrint',
                            array(
                                'label' => __('Invoice') . ' » ' . __('Print'),
                                //'url'   => $this->getUrl('ordermanage/*/massInvoiceCapture', array('_current'=>true, 'proceedInvoice' => true)),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'invoice-print')),
                                'fields' => array('order_ids')
                            )
                        );
                    }

                    // + Capture Payment
                    if ($_auth->isAllowed('Iksanika_Ordermanage::ma_capture'))
                    {
                        $this->getMassactionBlock()->addItem('massCapture',
                            array(
                                'label' => __('Capture'),
                                //'url'   => $this->getUrl('ordermanage/*/massInvoiceCapture', array('_current'=>true, 'proceedCapture' => true)),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'capture')),
                                'fields' => array('order_ids')
                            )
                        );
                    }

                    // + Ship
                    if ($_auth->isAllowed('Iksanika_Ordermanage::ma_ship')) {

                        // Ship
                        $this->getMassactionBlock()->addItem('saveShip',
                            array(
                                'label' => __('Ship'),
                                // 'url'   => $this->getUrl('ordermanage/*/massInvoiceCapture', array('_current'=>true, 'proceedShipment' => true)),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'ship')),
                                'fields' => $fields4Update,
                            )
                        );
                    }

                    // + Ship / Print
                    if ($_auth->isAllowed('Iksanika_Ordermanage::ma_ship_print'))
                    {
                        $this->getMassactionBlock()->addItem('saveShipPrint',
                            array(
                                'label' => __('Ship') . ' » ' . __('Print'),
                                // 'url'   => $this->getUrl('ordermanage/*/massInvoiceCapture', array('_current'=>true, 'proceedShipment' => true)),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'ship-print')),
                                'fields' => $fields4Update,
                            )
                        );
                    }

                    // + Invoice - Capture
                    if ($_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_capture'))
                    {
                        $this->getMassactionBlock()->addItem('massInvoiceCapture',
                            array(
                                'label' => __('Invoice') . ' » ' . __('Capture'),
                                //'url'   => $this->getUrl('ordermanage/*/massInvoiceCapture', array('_current'=>true, 'proceedInvoice' => true, 'proceedCapture' => true)),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'invoice-capture')),
                                'fields' => array('order_ids')
                            )
                        );
                    }

                    // + Invoice / Ship
                    if ($_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_capture_ship'))
                    {
                        $this->getMassactionBlock()->addItem('massInvoiceCaptureShip',
                            array(
                                'label' => __('Invoice') . $maCaptureTitle . ' » ' . __('Ship'),
                                //'url'   => $this->getUrl('ordermanage/ * /massInvoiceCapture', array('_current'=>true, 'proceedInvoice' => true, 'proceedCapture' => true, 'proceedShipment' => true)),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'invoice' . $maCaptureAction . '-ship')),
                                'fields' => $fields4Update,
                            )
                        );
                    }

                    // + Invoice / Ship / Print
                    if($_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_capture_ship_print'))
                    {
                        $this->getMassactionBlock()->addItem('massInvoiceCaptureShipPrint',
                            array(
                                'label' => __('Invoice') . $maCaptureTitle . ' » ' . __('Ship') . ' » ' . __('Print'),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'invoice' . $maCaptureAction . '-ship-print')),
                                'fields' => $fields4Update,
                            )
                        );
                    }

                    // + Invoice / Ship / Complete
                    if($_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_capture_ship_complete'))
                    {
                        // Invoice->Capture->Ship
                        $this->getMassactionBlock()->addItem('massInvoiceCaptureShipComplete',
                            array(
                                'label' => __('Invoice') . $maCaptureTitle . ' » ' . __('Ship') . ' » ' . __('Complete'),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'invoice' . $maCaptureAction . '-ship-complete')),
                                'fields' => $fields4Update,
                            )
                        );
                    }

                    // + Invoice / Ship / Complete / Print
                    if($_auth->isAllowed('Iksanika_Ordermanage::ma_invoice_capture_ship_complete_print'))
                    {
                        // Invoice->Capture->Ship
                        $this->getMassactionBlock()->addItem('massInvoiceCaptureShipCompletePrint',
                            array(
                                'label' => __('Invoice') . $maCaptureTitle . ' » ' . __('Ship') . ' » ' . __('Complete') . ' » ' . __('Print'),
                                'url' => $this->getUrl('ordermanage/*/massRunAction', array('_current' => true, 'trigger_actions' => 'invoice' . $maCaptureAction . '-ship-complete-print')),
                                'fields' => $fields4Update,
                            )
                        );
                    }

                    $this->getMassactionBlock()->addItem('otherDivider3-4', $this->getSubDivider('', true));

                }


                // + Complete Order
                if($_auth->isAllowed('Iksanika_Ordermanage::ma_complete'))
                {
                    $this->getMassactionBlock()->addItem('massComplete',
                        array(
                            'label' => __('Complete'),
                            'url'   => $this->getUrl('ordermanage/*/massRunAction', array('_current'=>true, 'trigger_actions' => 'complete')),
                            'fields' => array('order_ids')
                        )
                    );
                }

                // + Uncancel Order
                if($_auth->isAllowed('Iksanika_Ordermanage::ma_setprocessing'))
                {
                    $this->getMassactionBlock()->addItem('massSetProcessing',
                        array(
                            'label' => __('Uncancel'),
                            'url'   => $this->getUrl('ordermanage/*/massRunAction', array('_current'=>true, 'trigger_actions' => 'setprocessing')),
                            'fields' => $fields4Update,
                        )
                    );
                }


                // + Delete Order (canceled-only)
                if($_auth->isAllowed('Iksanika_Ordermanage::ma_delete'))
                {

                    $this->getMassactionBlock()->addItem('otherDivider2', $this->getSubDivider('', true));
                    $this->getMassactionBlock()->addItem('delete',
                        array(
                            'label' => __('Delete'),
                            'url'   => $this->getUrl('ordermanage/*/delete', array('_current'=>true)),
                            'fields' => $fields4Update,
                        )
                    );
                }
            }

        }
        
        return $this;
    }
    
    
    protected function getSubDivider($divider="-------", $empty = false) {
        $dividerTemplate = [
          'label' => !$empty ? ('--------'.__($divider).'--------') : (' '),
          'url'   => $this->getUrl('ordermanage/*/index', ['_current'=>true]),
          'callback' => "null"
        ];
        return $dividerTemplate;
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('ordermanage/*/grid', array('_current'=>true));
    }
        
    
    public function getCsv($exporType = \Iksanika\Ordermanage\Model\Export::ORDERS)
    {
        $csv = '';
        $this->_isExport = $exporType;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        $this->getCollection()->load();
        $this->_afterLoadCollection();
        $data = array();
        foreach ($this->getColumns() as $column) {
            if (!$column->getIsSystem()) {
                $data[] = '"'.$column->getExportHeader().'"';
            }
        }
        $csv.= implode(',', $data)."\n";
        
        foreach ($this->getCollection() as $item) {
            $data = array();
            foreach ($this->getColumns() as $column) {
                if (!$column->getIsSystem()) 
                {
                    $colIndex = $column->getIndex();
                    $colContent = (string)$item->getData($colIndex);
//                    if($colIndex == 'category_ids')
//                        $colContent = implode(',', $item->getCategoryIds());

                    if($this->_isExport == \Iksanika\Ordermanage\Model\Export::ORDERED_ITEMS)
                    {
                        $order = $item->getOrder();
                    }else
                    {
                        $order = $item;
                    }
                    
                    if($colIndex == 'billing_full_form')
                        $colContent = $this->addressRenderer->format($order->getBillingAddress(), 'text'); // html
                    
                    if($colIndex == 'billing_name')
                        $colContent = $order->getBillingAddress()->getName();
                    
                    if($colIndex == 'billing_street')
                    {
                        $streets = $order->getBillingAddress()->getStreet();
                        $colContent =  ($streets && isset($streets[0])) ? $streets[0] : '';
                    }
                    if($colIndex == 'billing_region')
                        $colContent = $order->getBillingAddress()->getRegion();
                    if($colIndex == 'billing_country')
                        $colContent = $order->getBillingAddress()->getCountry();
                    
                    if($colIndex == 'shipping_full_form')
                        $colContent = $this->addressRenderer->format($order->getShippingAddress(), 'text'); // html
                    
                    if($colIndex == 'shipping_name' && $order->getShippingAddress())
                        $colContent = $order->getShippingAddress()->getName();
                    
                    if($colIndex == 'shipping_street')
                    {
                        $streets = $order->getShippingAddress()->getStreet();
                        $colContent = ($streets && isset($streets[0])) ? $streets[0] : '';
                    }
                    if($colIndex == 'shipping_region')
                        $colContent = $order->getShippingAddress()->getRegion();
                    if($colIndex == 'shipping_country')
                        $colContent = $order->getShippingAddress()->getCountry();
                    
                    if($colIndex == 'shipping_tracking_number')
                    {
                        $trackingNumbers = $item->getTracksCollection();
                        if(count($trackingNumbers))
                        {
                            $trackNumber = '';
                            foreach($trackingNumbers as $track)
                            {
                                $trackNumber .= '<strong>'.(!$this->_scopeConfig->getValue("carriers/".$track['carrier_code']) ? 'Custom' : $this->_scopeConfig->getValue("carriers/".$track['carrier_code']."/title")).'</strong><br/>';
                                $trackNumber .= $track['track_number'];
                            }
                            $colContent = $trackNumber;
                        }else
                        {
                            //$_item->setData('shipping_tracking_number', '[NOT SPECIFIED]');
                        }
                    }
                    
                    if($colIndex == 'payment_method')
                        $colContent = $order->getPayment()->getMethodInstance()->getTitle();
                    if($colIndex == 'payment_code')
                        $colContent = $order->getPayment()->getMethodInstance()->getCode();
                    if($colIndex == 'payment_cc_type')
                        $colContent = $order->getPayment()->getData('cc_type') == null ? '[NO CC TYPE]' : $order->getPayment()->getData('cc_type');
                    
                    if($colIndex == 'special_qty_uniq_prods')
                        $colContent = count($order->getAllItems());
                    
                    if($colIndex == 'options' && ($this->_isExport == \Iksanika\Ordermanage\Model\Export::ORDERED_ITEMS))
                    {
                        $productOptions = $item->getProductOptions();
                        if($productOptions && isset($productOptions['options']))
                        {
                            foreach($productOptions['options'] as $optionId => $option)
                            {
                                $colContent .= $option['label'].': '.$option['value'].'\n';
                            }
                        }
                    }

                    if($column->getId() == 'ordered_item_status' && $colIndex == 'status' && ($this->_isExport == \Iksanika\Ordermanage\Model\Export::ORDERED_ITEMS))
                    {
                        $colContent = $item->getStatus();
                    }
                    $data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $colContent).'"';
                }
            }
            $csv.= implode(',', $data)."\n";
        }

        if ($this->getCountTotals())
        {
            $data = array();
            foreach ($this->getColumns() as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'),
                        $column->getRowFieldExport($this->getTotals())) . '"';
                }
            }
            $csv.= implode(',', $data)."\n";
        }
        return $csv;
    }
    
}