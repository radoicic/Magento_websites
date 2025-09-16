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

class CarrierCode implements \Magento\Framework\Option\ArrayInterface
{
    
    /**
     * @param \Iksanika\Ordermanage\Helper\Data $helperData
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Iksanika\Ordermanage\Helper\Data $helperData,
        \Magento\Shipping\Model\Config $shipConfig
    ) {
        $this->_helperData = $helperData;
        $this->_shipConfig = $shipConfig;
    }

    public function toOptionArray()
    {
        $carriers = array();
        $carrierInstances = $this->_shipConfig->getAllCarriers();
        $carriers['custom'] = __('Custom Value');
        foreach ($carrierInstances as $code => $carrier) 
        {
            if ($carrier->isTrackingAvailable()) 
            {
                $carriers[] = array('value' => $code, 'label' => $carrier->getConfigData('title'));
            }
        }
        return $carriers;
    }
}