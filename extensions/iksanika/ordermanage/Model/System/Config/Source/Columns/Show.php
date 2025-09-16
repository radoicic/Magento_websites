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

/**
 * Used in creating options for Yes|No config value selection
 *
 */
namespace Iksanika\Ordermanage\Model\System\Config\Source\Columns;

class Show implements \Magento\Framework\Option\ArrayInterface
{
    
    public static $columnsTitleDefaults = array(
        'increment_id'      => 'Order #',
        'store_id'          => 'Purchased From (Store)',
        'created_at'        => 'Purchased On',
        'billing_name'      => 'Bill to Name',
        'shipping_name'     => 'Ship to Name',
        'base_grand_total'  => 'G.T. (Base)',
        'grand_total'       => 'G.T. (Purchased)',
        'status'            => 'Status',
    );

    public static $columnsTitleProducts = array(
        'name'  => 'Products Name',
        'sku'   => 'Products SKU',
    );
    
    public static $columnsTitleBill = array(
        'billing_full_form'     => 'Billing Address',
        
        'billing_firstname'     => 'First Name',
        'billing_middlename'    => 'Middle Name',
        'billing_lastname'      => 'Last Name',
        'billing_company'       => 'Company',
        'billing_street'        => 'Street',
        'billing_city'          => 'City',
        'billing_region'        => 'Region',
        'billing_postcode'      => 'Postcode',
        'billing_email'         => 'Email',
        'billing_telephone'     => 'Telephone',
        'billing_country'       => 'Country',
        'billing_fax'           => 'Fax',
        
        
        'shipping_full_form'    => 'Shipping Address',
        
        'shipping_firstname'    => 'First Name',
        'shipping_middlename'   => 'Middle Name',
        'shipping_lastname'     => 'Last Name',
        'shipping_company'      => 'Company',
        'shipping_street'       => 'Street',
        'shipping_city'         => 'City',
        'shipping_region'       => 'Region',
        'shipping_postcode'     => 'Postcode',
        'shipping_email'        => 'Email',
        'shipping_telephone'    => 'Telephone',
        'shipping_country'      => 'Country',
        'shipping_fax'          => 'Fax',
        
        'shipping_tracking_number' => 'Tracking Number',
    );

    public static $columnsTitlePayment = array(
        'payment_method'    => 'Payment Method',
        'payment_code'      => 'Payment Code',
        'payment_cc_type'   => 'CC Type',
    );

    public static $columnsTitleSpecial = array(
        'special_qty_uniq_prods' => 'Qty Ordered Unique Products',
    );
    
    public static $columnsTitleOthers = array(
        '' => '',
    );
    
    /**
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $eavAttributeColletion,
        \Magento\Eav\Model\Entity $eavEntity,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->eavAttributeCollection = $eavAttributeColletion;
        $this->eavEntity = $eavEntity;
        $this->_resource = $resource;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        
        $defaultGridColumns = self::$columnsTitleDefaults;
        
        $columnsTitle = array_merge(self::$columnsTitleDefaults, self::$columnsTitleProducts, self::$columnsTitleBill, self::$columnsTitlePayment, self::$columnsTitleSpecial);
        
        $columns =array();
        //$columnsCollection = Mage::getResourceModel('sales/order');
        //var_dump($columnsCollection);
/*        $columnsCollection = 
            Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter( Mage::getModel('eav/entity')->setType('sales_order')->getTypeId() )
	    ->addFilter("is_visible", 1);*/
        
        // add default grid items
        $columns[] = array('value' => 'increment_id', 'label' => '[DEFAULT GRID] '.$columnsTitle['increment_id']);
        $columns[] = array('value' => 'store_id', 'label' => '[DEFAULT GRID] '.$columnsTitle['store_id']);
        $columns[] = array('value' => 'created_at', 'label' => '[DEFAULT GRID] '.$columnsTitle['created_at']);
        $columns[] = array('value' => 'billing_name', 'label' => '[DEFAULT GRID] '.$columnsTitle['billing_name']);
        $columns[] = array('value' => 'shipping_name', 'label' => '[DEFAULT GRID] '.$columnsTitle['shipping_name']);
        $columns[] = array('value' => 'base_grand_total', 'label' => '[DEFAULT GRID] '.$columnsTitle['base_grand_total']);
        $columns[] = array('value' => 'grand_total', 'label' => '[DEFAULT GRID] '.$columnsTitle['grand_total']);
        $columns[] = array('value' => 'status', 'label' => '[DEFAULT GRID] '.$columnsTitle['status']);
        
        // add billing data
        $columns[] = array('value' => 'name', 'label' => '[PRODUCTS Data] '.$columnsTitle['name']);
        $columns[] = array('value' => 'sku', 'label' => '[PRODUCTS Data] '.$columnsTitle['sku']);
        
        // add billing data
        $columns[] = array('value' => 'billing_full_form', 'label' => '[BILLING Addr] '.$columnsTitle['billing_full_form']);
        
        $columns[] = array('value' => 'billing_firstname', 'label' => '[BILLING Addr] '.$columnsTitle['billing_firstname']);
        $columns[] = array('value' => 'billing_middlename', 'label' => '[BILLING Addr] '.$columnsTitle['billing_middlename']);
        $columns[] = array('value' => 'billing_lastname', 'label' => '[BILLING Addr] '.$columnsTitle['billing_lastname']);
        $columns[] = array('value' => 'billing_company', 'label' => '[BILLING Addr] '.$columnsTitle['billing_company']);
        $columns[] = array('value' => 'billing_street', 'label' => '[BILLING Addr] '.$columnsTitle['billing_street']);
        $columns[] = array('value' => 'billing_city', 'label' => '[BILLING Addr] '.$columnsTitle['billing_city']);
        $columns[] = array('value' => 'billing_region', 'label' => '[BILLING Addr] '.$columnsTitle['billing_region']);
        $columns[] = array('value' => 'billing_postcode', 'label' => '[BILLING Addr] '.$columnsTitle['billing_postcode']);
        $columns[] = array('value' => 'billing_email', 'label' => '[BILLING Addr] '.$columnsTitle['billing_email']);
        $columns[] = array('value' => 'billing_telephone', 'label' => '[BILLING Addr] '.$columnsTitle['billing_telephone']);
        $columns[] = array('value' => 'billing_country', 'label' => '[BILLING Addr] '.$columnsTitle['billing_country']);
        $columns[] = array('value' => 'billing_fax', 'label' => '[BILLING Addr] '.$columnsTitle['billing_fax']);
        
        // add shipping data
        $columns[] = array('value' => 'shipping_full_form', 'label' => '[SHIPPING Addr] '.$columnsTitle['shipping_full_form']);
        
        $columns[] = array('value' => 'shipping_firstname', 'label' => '[SHIPPING Addr] '.$columnsTitle['shipping_firstname']);
        $columns[] = array('value' => 'shipping_middlename', 'label' => '[SHIPPING Addr] '.$columnsTitle['shipping_middlename']);
        $columns[] = array('value' => 'shipping_lastname', 'label' => '[SHIPPING Addr] '.$columnsTitle['shipping_lastname']);
        $columns[] = array('value' => 'shipping_company', 'label' => '[SHIPPING Addr] '.$columnsTitle['shipping_company']);
        $columns[] = array('value' => 'shipping_street', 'label' => '[SHIPPING Addr] '.$columnsTitle['shipping_street']);
        $columns[] = array('value' => 'shipping_city', 'label' => '[SHIPPING Addr] '.$columnsTitle['shipping_city']);
        $columns[] = array('value' => 'shipping_region', 'label' => '[SHIPPING Addr] '.$columnsTitle['shipping_region']);
        $columns[] = array('value' => 'shipping_postcode', 'label' => '[SHIPPING Addr] '.$columnsTitle['shipping_postcode']);
        $columns[] = array('value' => 'shipping_email', 'label' => '[SHIPPING Addr] '.$columnsTitle['shipping_email']);
        $columns[] = array('value' => 'shipping_telephone', 'label' => '[SHIPPING Addr] '.$columnsTitle['shipping_telephone']);
        $columns[] = array('value' => 'shipping_country', 'label' => '[SHIPPING Addr] '.$columnsTitle['shipping_country']);
        $columns[] = array('value' => 'shipping_fax', 'label' => '[SHIPPING Addr] '.$columnsTitle['shipping_fax']);
        $columns[] = array('value' => 'shipping_tracking_number', 'label' => '[SHIPPING Addr] '.$columnsTitle['shipping_tracking_number']);
        
        // add other data
        $columns[] = array('value' => 'payment_method', 'label' => '[PAYMENT Info] '.$columnsTitle['payment_method']);
        $columns[] = array('value' => 'payment_code', 'label' => '[PAYMENT Info] '.$columnsTitle['payment_code']);
        $columns[] = array('value' => 'payment_cc_type', 'label' => '[PAYMENT Info] '.$columnsTitle['payment_cc_type']);

        $columns[] = array('value' => 'special_qty_uniq_prods', 'label' => '[SPECIAL Info] '.$columnsTitle['special_qty_uniq_prods']);
         
        // add other data
        $existColumns = $columns;
//        die('test test');
//        $resource       =   Mage::getSingleton('core/resource');
//        $readConnection =   $resource->getConnection('core_read');
//        $tableName      =   $resource->getTableName('sales/order');
        
        $tableName = $this->_resource->getTableName('sales_order');
        $readConnection = $this->_resource->getConnection('core_read');
        
        $columnsCollection = $readConnection->fetchAll("SHOW COLUMNS FROM ".$tableName);
        
        if($columnsCollection && count($columnsCollection) > 0)
        {
            foreach($columnsCollection as $column) 
            {
                if(!isset($defaultGridColumns[$column['Field']]))
                {
                    $columns[] = array(
                        'value' => $column['Field'],
                        'label' => isset($columnsTitle[$column['Field']]) ? $columnsTitle[$column['Field']] : ucwords(str_replace('_', ' ', $column['Field']))
                    );
                }
            }
        }
        return $columns;

        /*
        
        $columns = [
            [
                'value' => 'id',   
                'label' => __('ID')
            ],
            [
                'value' => 'type_id',   
                'label' => __('Type (simple, bundle, etc)')
            ],
            [
                'value' => 'attribute_set_id',   
                'label' => __('Attribute Set')
            ],
            [
                'value' => 'qty',   
                'label' => __('Quantity')
            ],
            [
                'value' => 'is_in_stock',   
                'label' => __('Is in Stock')
            ],
            [
                'value' => 'websites',   
                'label' => __('Websites')
            ],
            [
                'value' => 'category_ids',   
                'label' => __('Category ID\'s')
            ],
            [
                'value' => 'category',   
                'label' => __('Categories')
            ],
            [
                'value' => 'related_ids',   
                'label' => __('Related: Relative Products IDs')
            ],
            [
                'value' => 'cross_sell_ids',   
                'label' => __('Related: Cross-Sell Products IDs')
            ],
            [
                'value' => 'up_sell_ids',   
                'label' => __('Related: Up-Sell Products IDs')
            ],
            [
                'value' => 'associated_groupped_ids',
                'label' => __('Associated IDs: for Groupped')
            ],
            [
                'value' => 'associated_configurable_ids',
                'label' => __('Associated IDs: for Configurable')
            ],
        ];
        
        $columnsCollection = $this->eavAttributeCollection->setEntityTypeFilter($this->eavEntity->setType('catalog_product')->getTypeId())->addFilter('is_visible', 1);

        foreach($columnsCollection->getItems() as $column) 
        {
            if($column->getAttributeCode() != 'quantity_and_stock_status')
            {
                $columns[] = [
                    'value' => $column->getAttributeCode(),   
                    'label' => $column->getFrontendLabel()
                ];
            }
        }
        
        return $columns;
        */
        
        
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $returnArray = [];
        foreach($this->toOptionArray() as $item)
        {
            $returnArray[$item['value']] = $item['label'];
        }
        return $returnArray;
    }
}
