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

class StatusComplete implements \Magento\Framework\Option\ArrayInterface
{
    
    /**
     * @param \Iksanika\Ordermanage\Helper\Data $helperData
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(\Iksanika\Ordermanage\Helper\Data $helperData)
    {
        $this->_helperData = $helperData;
    }

    public function toOptionArray()
    {
        $statusList = $this->_helperData->getStatusesByState('complete');
        
        $columns    = array(array('value' => 'default', 'label' => 'Magento Default'));
        foreach($statusList as $statusItemValue => $statusItemLabel)
        {
            $columns[] = array('value' => $statusItemValue, 'label' => $statusItemLabel);
        }
        return $columns;
        
    }
}