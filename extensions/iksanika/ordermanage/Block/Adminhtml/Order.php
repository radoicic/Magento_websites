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

namespace Iksanika\Ordermanage\Block\Adminhtml;

use \Magento\Backend\Block\Widget\Container;

class Order extends \Magento\Catalog\Block\Adminhtml\Product 
{

    /**
     * Prepare button and grid
     *
     * @return \Magento\Catalog\Block\Adminhtml\Product
     */
    protected function _prepareLayout()
    {
        $this->addButton(
            'add',
            [
                'label' => 'Create New Order',
                'onclick' => 'setLocation(\'' . $this->getCreateUrl() . '\')',
                'class' => 'add primary'
            ]
        );
        
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Iksanika\Ordermanage\Block\Adminhtml\Order\Grid', 'product.grid')
//            $this->getLayout()->createBlock('Magento\Catalog\Block\Adminhtml\Product\Grid', 'product.grid')
        );
        return \Magento\Backend\Block\Widget\Container::_prepareLayout();
    }

    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
    
    public function getCreateUrl()
    {
        return $this->getUrl('sales/order_create/start');
    }

}
