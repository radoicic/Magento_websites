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

class OrderStatus extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options
{

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     * @param \Iksanika\Ordermanage\Helper\Data $helperData
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Iksanika\Ordermanage\Helper\Data $helperData,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Shipping\Model\Config $shipConfig,
        \Magento\Sales\Model\Order\Shipment\Track $orderShipmentTrack,
        array $data = []
    ) {
        parent::__construct($context, $data);
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
        $statusList = $this->_helperData->getStatusesByState($row->getData('state'));
        if (!empty($statusList) && is_array($statusList)) 
        {
            $value = $row->getData($this->getColumn()->getIndex());
            $out = '<select name="'.$this->getColumn()->getIndex().'">';
            foreach($statusList as $itemId => $item)
            {
                $out .= '<option value="'.$itemId.'" '.($value == $itemId ? 'selected':'').'>'.$this->escapeHtml($item).'</option>'; 
            }
            $out .= '</select>';
            return $out;
        }
        return '[SELECT is empty]';
    }
    
}
