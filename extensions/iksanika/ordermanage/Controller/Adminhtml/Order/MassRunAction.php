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

use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\App\Config;
use Magento\Sales\Model\Order\Invoice;
use Psr\Log\LoggerInterface;


class MassRunAction extends \Iksanika\Ordermanage\Controller\Adminhtml\Order\Action\SuperAction
{
    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        //$fa = $this->fetchActionsToRun();
        $actionsList        =   $this->getRequest()->getParam('trigger_actions');
        $_authStatus = false;
        if ($actionsList == 'invoice' ||
            $actionsList == 'invoice-notify')
        {
            $_authStatus = $this->_authorization->isAllowed('Iksanika_Ordermanage::ma_invoice');
        }
        if ($actionsList == 'invoice-print' ||
            $actionsList == 'invoice-print-notify')
        {
            $_authStatus = $this->_authorization->isAllowed('Iksanika_Ordermanage::ma_invoice_print');
        }
        if($actionsList == 'capture')
        {
            $_authStatus = $this->_authorization->isAllowed('Iksanika_Ordermanage::ma_capture');
        }
        if ($actionsList == 'ship' ||
            $actionsList == 'ship-notify')
        {
            $_authStatus = $this->_authorization->isAllowed('Iksanika_Ordermanage::ma_ship');
        }
        if ($actionsList == 'ship-print' ||
            $actionsList == 'ship-print-notify')
        {
            $_authStatus = $this->_authorization->isAllowed('Iksanika_Ordermanage::ma_ship_print');
        }
        if ($actionsList == 'invoice-capture' ||
            $actionsList == 'invoice-capture-notify')
        {
            $_authStatus = $this->_authorization->isAllowed('Iksanika_Ordermanage::ma_invoice_capture');
        }
        if ($actionsList == 'invoice-capture-ship' ||
            $actionsList == 'invoice-capture-ship-notify' ||
            $actionsList == 'invoice-ship' ||
            $actionsList == 'invoice-ship-notify')
        {
            $_authStatus = $this->_authorization->isAllowed('Iksanika_Ordermanage::ma_invoice_capture_ship');
        }
        if ($actionsList == 'invoice-capture-ship-print' ||
            $actionsList == 'invoice-capture-ship-print-notify' ||
            $actionsList == 'invoice-ship-print' ||
            $actionsList == 'invoice-ship-print-notify')
        {
            $_authStatus = $this->_authorization->isAllowed('Iksanika_Ordermanage::ma_invoice_capture_ship_print');
        }
        if ($actionsList == 'invoice-capture-ship-complete' ||
            $actionsList == 'invoice-capture-ship-complete-notify' ||
            $actionsList == 'invoice-ship-complete' ||
            $actionsList == 'invoice-ship-complete-notify')
        {
            $_authStatus = $this->_authorization->isAllowed('Iksanika_Ordermanage::ma_invoice_capture_ship_complete');
        }
        if ($actionsList == 'invoice-capture-ship-complete-print' ||
            $actionsList == 'invoice-capture-ship-complete-print-notify' ||
            $actionsList == 'invoice-ship-complete-print' ||
            $actionsList == 'invoice-ship-complete-print-notify')
        {
            $_authStatus = $this->_authorization->isAllowed('Iksanika_Ordermanage::ma_invoice_capture_ship_complete_print');
        }
        if($actionsList == 'sendorderemail')
        {
            $_authStatus = $this->_authorization->isAllowed('Iksanika_Ordermanage::ma_sendorderemail');
        }
        if($actionsList == 'invoice-sendemail')
        {
            $_authStatus = $this->_authorization->isAllowed('Iksanika_Ordermanage::ma_invoice_sendemail');
        }
        if($actionsList == 'ship-sendemail')
        {
            $_authStatus = $this->_authorization->isAllowed('Iksanika_Ordermanage::ma_ship_sendemail');
        }
        if($actionsList == 'complete')
        {
            $_authStatus = $this->_authorization->isAllowed('Iksanika_Ordermanage::ma_complete');
        }
        if($actionsList == 'setprocessing')
        {
            $_authStatus = $this->_authorization->isAllowed('Iksanika_Ordermanage::ma_setprocessing');
        }
        if($actionsList == 'setstatus')
        {
            $_authStatus = $this->_authorization->isAllowed('Iksanika_Ordermanage::ma_setstatus');
        }
        return $_authStatus;
    }
}