<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_FacebookShop
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\FacebookShop\Block\Adminhtml\GoogleProducts;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollection;

class Products extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var  \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ProductCollection $productCollection
     */
    protected $productCollection;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param ProductCollection $productCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        ProductCollection $productCollection,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->productCollection = $productCollection;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('facebookshop_products_grid');
        $this->setDefaultSort('id', 'desc');
        $this->setUseAjax(true);
    }

    /**
     * Apply various selection filters to prepare collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->productCollection->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('is_facebook_product', 1);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

   /**
    * prepare columns for grid
    *
    * @return void
    */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'assign_gpc',
            [
                'type' => 'checkbox',
                'name' => 'assign_gpc',
                'values' => '',
                'index' => 'id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );
        $this->addColumn(
            'entity_id',
            [
                    'header' => __('Product Id'),
                    'sortable' => true,
                    'index' => 'entity_id',
                    'header_css_class' => 'col-id',
                    'column_css_class' => 'col-id'
                ]
        );
            $this->addColumn(
                'name',
                [
                    'header' => __('Product Name'),
                    'index' => 'name'
                ]
            );
            $this->addColumn(
                'sku',
                [
                    'header' => __('Sku'),
                    'index' => 'sku'
                ]
            );
            $this->addColumn(
                'google_product_category',
                [
                    'header' => __('Google Product category'),
                    'index' => 'google_product_category'
                ]
            );
        return parent::_prepareColumns();
    }

   /**
    * grid url
    *
    * @return void
    */
    public function getGridUrl()
    {
        return $this->getUrl('facebookshop/googleproducts/grid', ['_current' => true]);
    }
}
