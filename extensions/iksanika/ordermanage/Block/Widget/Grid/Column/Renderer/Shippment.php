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
namespace Iksanika\Ordermanage\Block\Widget\Grid\Column\Renderer;

class Shippment extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options
{

    /**
     * @var \Magento\Sales\Model\Service\OrderFactory
     */
    protected $_serviceOrderFactory;

    /**
     * @param \Iksanika\Ordermanage\Helper\Data $helperData
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Iksanika\Ordermanage\Helper\Data $helperData,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Shipping\Model\Config $shipConfig,
        \Magento\Sales\Model\Order\Shipment\Track $orderShipmentTrack
    ) {
        $this->_helperData = $helperData;
        $this->_orderFactory = $orderFactory;
        $this->_shipConfig = $shipConfig;
        $this->_orderShipmentTrack = $orderShipmentTrack;
    }
    
    /**
     * Render a grid cell as options
     *
     * @param \Magento\Framework\Object $row
     * @return string|void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $html   =   '';
        $order  =   $this->_orderFactory->create()->load($row->getId());
        
        if($order->canShip())
        {
            
            $default    =  $this->_helperData->getScopeConfig()->getValue('iksanika_ordermanage/ship/carrier');
            
            $html       =  __('Carrier:') . '<br/>';
            $html       .=  '<select class="ordermanage-carrier" rel="'.$row->getId().'"  name="'.$this->getColumn()->getIndex().'_carrier" style="width:90%">';
            foreach ($this->getCarriers($row->getIncrementId()) as $k => $v)
            {
                $selected = '';
                if ($default == $k)
                {
                    $selected = 'selected="selected"';
                }
                $html .= sprintf('<option value="%s" %s>%s</option>', $k, $selected, $v);
            }
            $html .= '</select><br/>';
/*
            if (Mage::getStoreConfig('ordermanage/ship/comment')) 
            {
                $html .= Mage::helper('sales')->__('Title:') . '<br />';
                $html .= '<input rel="'.$row->getId().'" class="input-text amasty-comment" value="'.Mage::getStoreConfig('amoaction/ship/title').'" /><br />';
            }
*/            
            $html .= __('Tracking Number:') . '<br/>';
            $html .= '<input rel="'.$row->getId().'" class="input-text" name="'.$this->getColumn()->getIndex().'" value=""/>';
        }else 
        {
            
            $field = 'track_number';
//            if (version_compare(Mage::getVersion(), '1.5.1.0') <= 0)
//            {
//                $field = 'number';
//            }            
            
//            $collection = Mage::getModel('sales/order_shipment_track')
            $collection = $this->_orderShipmentTrack
                ->getCollection()
                ->addAttributeToSelect($field)
                ->addAttributeToSelect('title')
                ->setOrderFilter($row->getId());
                
            $numbers    =   array();
            $carriers   = array();
            foreach ($collection as $track) 
            {
                $numbers[]  = $track->getData($field);
                $carriers[] = $track->getTitle();
            }

            if($carriers)
            {
                $html =  __('Carrier:').'<br />';
                $html .= '<strong>' . implode(', ', $carriers) . '</strong><br />';
                
                $html .= __('Tracking Number:').'<br />';
                $html .= '<strong>' .implode(', ', $numbers). '</strong>';
            }
        }

        return $html;
    
    }

    private function getCarriers($code)
    {
        $carriers = array();
        $carrierInstances = $this->_shipConfig->getAllCarriers();
        $carriers['custom'] = __('Custom Value');
        foreach ($carrierInstances as $code => $carrier) 
        {
            if ($carrier->isTrackingAvailable()) 
            {
//                $carriers[] = array('value' => $code, 'label' => $carrier->getConfigData('title'));
                $carriers[$code] = $carrier->getConfigData('title');
            }
        }
        return $carriers;
    }
}
