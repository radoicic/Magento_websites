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

namespace Iksanika\Ordermanage\Model\System\Config\Source\Columns;

class Products implements \Magento\Framework\Option\ArrayInterface
{
    
    public static $columnsTitleProducts = array(
        'small_image' => 'Small Image',
        'name' => 'Product Name',
        'options' => 'Product Options',
        'sku' => 'SKU',
        'product_id' => 'Product Id',
        'weight' => 'Weight',
        'qty_ordered' => 'Quantity',
        'status' => 'Item Status',
        'original_price' => 'Original Price',
        'price' => 'Price',
        'subtotal' => 'Subtotal',
        'tax_amount' => 'Tax Amount',
        'tax_percent' => 'Tax Percent',
        'discount_amount' => 'Discount Amount',
        'row_total' => 'Row Total',
        'be_link' => 'Backend Presence',
        'fe_link' => 'Frontend Presence',
    );
    
    public function toOptionArray()
    {
        $columns =array();
        
        // add products data
        $columns[] = array('value' => 'small_image', 'label' => self::$columnsTitleProducts['small_image']);
        $columns[] = array('value' => 'name', 'label' => self::$columnsTitleProducts['name']);
        $columns[] = array('value' => 'options', 'label' => self::$columnsTitleProducts['options']);
        $columns[] = array('value' => 'sku', 'label' => self::$columnsTitleProducts['sku']);
        $columns[] = array('value' => 'product_id', 'label' => self::$columnsTitleProducts['product_id']);
        $columns[] = array('value' => 'weight', 'label' => self::$columnsTitleProducts['weight']);
        $columns[] = array('value' => 'qty_ordered', 'label' => self::$columnsTitleProducts['qty_ordered']);
        $columns[] = array('value' => 'status', 'label' => self::$columnsTitleProducts['status']);
        $columns[] = array('value' => 'original_price', 'label' => self::$columnsTitleProducts['original_price']);
        $columns[] = array('value' => 'price', 'label' => self::$columnsTitleProducts['price']);
        $columns[] = array('value' => 'subtotal', 'label' => self::$columnsTitleProducts['subtotal']);
        $columns[] = array('value' => 'tax_amount', 'label' => self::$columnsTitleProducts['tax_amount']);
        $columns[] = array('value' => 'tax_percent', 'label' => self::$columnsTitleProducts['tax_percent']);
        $columns[] = array('value' => 'discount_amount', 'label' => self::$columnsTitleProducts['discount_amount']);
        $columns[] = array('value' => 'row_total', 'label' => self::$columnsTitleProducts['row_total']);
        $columns[] = array('value' => 'be_link', 'label' => self::$columnsTitleProducts['be_link']);
        $columns[] = array('value' => 'fe_link', 'label' => self::$columnsTitleProducts['fe_link']);

        
        return $columns;
    }
}