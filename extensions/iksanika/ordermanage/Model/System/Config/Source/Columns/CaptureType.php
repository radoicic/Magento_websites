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

class CaptureType implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        $captures = array();
        $captures[] = array('value' => \Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE, 'label' => 'Capture Online');
        $captures[] = array('value' => \Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE, 'label' => 'Capture Offline');
        $captures[] = array('value' => \Magento\Sales\Model\Order\Invoice::NOT_CAPTURE, 'label' => 'Not Capture');
        return $captures;
    }
}