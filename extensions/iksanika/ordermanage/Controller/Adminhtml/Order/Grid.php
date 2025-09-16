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
namespace Iksanika\Ordermanage\Controller\Adminhtml\Order;

class Grid extends \Magento\Catalog\Controller\Adminhtml\Product\Grid 
{
    /**
     * Product grid for AJAX request
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        return $this->resultPageFactory->create();
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Iksanika_Ordermanage::ordermanage');
    }
}
