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

class Mode implements \Magento\Framework\Option\ArrayInterface
{
    const MODE_STANDARD     =   'MODE_STANDARD';
    const MODE_ORDER_ITEMS  =   'MODE_ORDER_ITEMS';

    public function toOptionArray()
    {
        $captures = array();
        $captures[] = array('value' => \Iksanika\Ordermanage\Model\System\Config\Source\Columns\Mode::MODE_STANDARD, 'label' => 'Order based (Standard)');
        $captures[] = array('value' => \Iksanika\Ordermanage\Model\System\Config\Source\Columns\Mode::MODE_ORDER_ITEMS, 'label' => 'Ordered items based');
        
        return $captures;
    }
}