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
namespace Iksanika\Ordermanage\Controller\Adminhtml\Order\Action;

use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Config;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class SuperAction extends \Magento\Sales\Controller\Adminhtml\Order
{

    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config $config,
        \Magento\Sales\Model\Order\Config $orderStatuses,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender,
        \Iksanika\Ordermanage\Helper\Data $helper
//        ,\Magento\Backend\Helper\Data $helperBackend

    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_fileFactory = $fileFactory;
        $this->_translateInline = $translateInline;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->resultRawFactory = $resultRawFactory;
        parent::__construct($context, $coreRegistry, $fileFactory, $translateInline, $resultPageFactory, $resultJsonFactory, $resultLayoutFactory, $resultRawFactory, $orderManagement, $orderRepository, $logger);
        $this->_scopeConfig = $config;
        $this->_trackFactory = $trackFactory;
        $this->_shipmentLoader = $shipmentLoader;
        $this->_shipmentSender = $shipmentSender;

        $this->_orderStatuses = $orderStatuses;
        $this->_orderSender = $orderSender;
        $this->_invoiceSender = $invoiceSender;
        $this->_orderFactory = $orderFactory;
        $this->_transactionFactory = $transactionFactory;
        $this->_shipmentFactory = $shipmentFactory;

//        $this->_helperBackend = $helperBackend;
        $this->_helperBackend = $this->_helper;

        $this->_helperData = $helper;
        $this->_helperData->setScopeConfig($config);
    }

    public function execute()
    {
        @set_time_limit(0);

        $orderIds = $this->getRequest()->getParam('order_ids');
        if(is_array($orderIds))
        {
            $this->proceedActions();
        }else
        {
            $this->messageManager->addError(__('Please select product(s)').'. '.__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('ordermanage/*/', ['_current' => true, '_query' => 'st=1']); //'store' => $storeId]
    }

    /**
     * @param $orderStatuses
     * @param $statusComplete
     * @param $statusInvoice
     * @param $statusCapture
     * @param $statusShip
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function areStatusesValid($orderStatuses, $statusComplete, $statusInvoice, $statusCapture, $statusShip)
    {
        if(!array_key_exists($statusComplete, $orderStatuses) && $statusComplete !== 'default')
        {
            $this->messageManager->addComplexErrorMessage('messageHtmlNotice', ['content' => (string)
                __(
                    'Order status (code: %1) which should be set after completing order doesn\'t exist anymore. Please, '.
                    'make sure you set a valid order status on <a href="%2" target="_blank">extension settings page</a>. '.
                    'Please, try again after update.',
                    $statusComplete,
                    $this->_helperBackend->getUrl('adminhtml/system_config/edit', array('section'=> 'iksanika_ordermanage'))
                )]
            );
            return false;
        }
        if(!array_key_exists($statusInvoice, $orderStatuses) && ($statusInvoice !== 'default'))
        {
            $this->messageManager->addComplexErrorMessage('messageHtmlNotice', ['content' => (string)
                __(
                    'Order status (code: %1) which should be set after invoicing order doesn\'t exist anymore. Please, '.
                    'make sure you set a valid order status on <a href="%2" target="_blank">extension settings page</a>. '.
                    'Please, try again after update.',
                    $statusInvoice,
                    $this->_helperBackend->getUrl('adminhtml/system_config/edit', array('section'=> 'iksanika_ordermanage'))
                )]
            );
            return false;
        }
        if(!array_key_exists($statusCapture, $orderStatuses) && ($statusCapture !== 'default'))
        {
            $this->messageManager->addComplexErrorMessage('messageHtmlNotice', ['content' => (string)
                __(
                    'Order status (code: %1) which should be set after invoicing order doesn\'t exist anymore. Please, '.
                    'make sure you set a valid order status on <a href="%2" target="_blank">extension settings page</a>. '.
                    'Please, try again after update.',
                    $statusCapture,
                    $this->_helperBackend->getUrl('adminhtml/system_config/edit', array('section'=> 'iksanika_ordermanage'))
                )]
            );
            return false;
        }
        if(!array_key_exists($statusShip, $orderStatuses) && ($statusShip !== 'default'))
        {
            $this->messageManager->addComplexErrorMessage('messageHtmlNotice', ['content' => (string)
                __(
                    'Order status (code: %1) which should be set after shipping order doesn\'t exist anymore. Please, '.
                    'make sure you set a valid order status on <a href="%2" target="_blank">extension settings page</a>. '.
                    'Please, try again after update.',
                    $statusShip,
                    $this->_helperBackend->getUrl('adminhtml/system_config/edit', array('section'=> 'iksanika_ordermanage'))
                )]
            );
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    public function fetchActionsToRun()
    {
        $actionsList        =   $this->getRequest()->getParam('trigger_actions');
        return array(
            'invoice'       => strstr($actionsList, 'invoice') ? true : false,
            'capture'       => strstr($actionsList, 'capture') ? true : false,
            'ship'          => strstr($actionsList, 'ship') ? true : false,
            'complete'      => strstr($actionsList, 'complete') ? true : false,
            'print'         => strstr($actionsList, 'print') ? true : false,
            'setstatus'     => strstr($actionsList, 'setstatus') ? true : false,

            'notify'        => strstr($actionsList, 'notify') ? true : false,
            'sendemail'     => strstr($actionsList, 'sendemail') ? true : false,
            'sendorderemail'=> strstr($actionsList, 'sendorderemail') ? true : false,
            'setprocessing' => strstr($actionsList, 'setprocessing') ? true : false
        );
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function proceedActions()
    {
        /**
         * Initialization and check data
         */
        $orderIds       =   $this->getRequest()->getParam('order_ids');
        if(!is_array($orderIds)) // empty($actionsToRun)
        {
            $this->messageManager->addErrorMessage(
                __('Please, select order(s)').'. '.
                __('You should select checkboxes for each order row which should be proceeded. You can click on checkboxes in first column or use CTRL+Click on product row which should be selected.')
            );
            return false;
        }

        // Order status modifications
        $orderStatuses  =   $this->_orderStatuses->getStatuses();

        $statusInvoice  =   $this->_scopeConfig->getValue('iksanika_ordermanage/invoice/status');
        $statusCapture  =   $this->_scopeConfig->getValue('iksanika_ordermanage/capture/status');
        $statusShip     =   $this->_scopeConfig->getValue('iksanika_ordermanage/ship/status');
        $statusComplete =   $this->_scopeConfig->getValue('iksanika_ordermanage/complete/status');

        if(!$this->areStatusesValid($orderStatuses, $statusComplete, $statusInvoice, $statusCapture, $statusShip))
        {
            return false;
        }

        // Check which action should be triggered
        $fa = $this->fetchActionsToRun();
        list($runInvoice,     $runCapture,    $runShip,    $runComplete,    $runPrint,    $runSetStatus,    $runNotify,    $runSendEmail,    $runSendOrderEmail,    $runSetProcessing) =
        array($fa['invoice'], $fa['capture'], $fa['ship'], $fa['complete'], $fa['print'], $fa['setstatus'], $fa['notify'], $fa['sendemail'], $fa['sendorderemail'], $fa['setprocessing']);

        if($runSetStatus)
        {
            $newOrderStatus = $this->getRequest()->getParam('new-order-status');
        }

        /**
         * Apply required actions to selected list of orders
         */
        $updatedAmount = 0;
        foreach ($orderIds as $itemId => $orderId)
        {
            try
            {
                $isUpdated = false;

                /** @var \Magento\Sales\Model\Order $order */
                $order = $this->_orderFactory->create()->load($orderId);
                if(!$order || !$order->getId())
                {
                    $this->messageManager->addErrorMessage(__('The order (entity_id: %1) no longer exists.', $orderId));
                    continue;
                }

                /**
                 * Unhold order if actions should be applied to order which are on hold
                 */
                if(($runInvoice || $runShip || $runComplete) && ($order->getStatus() == \Magento\Sales\Model\Order::STATE_HOLDED))
                {
                    $order->unhold()->save();
                }

                /*
                 * Generate invoice and send/resend email handlers
                 */
                if($runInvoice)
                {
                    if($order->canInvoice() && !$runSendEmail)
                    {
                        if($this->proceedOrderInvoice($order, $runNotify, $runCapture))
                        {
                            $isUpdated = true;
                        }
                    }
                    if($runSendEmail)
                    {
                        $invoice    =   $order->getInvoiceCollection()->getFirstItem();
                        if($invoice->getId())
                        {
                            /** @var \Magento\Sales\Model\Order\Invoice $invoice */
                            $invoice->setCustomerNoteNotify(true);
                            $this->_invoiceSender->send($invoice);
                            $invoice->save();
                            $isUpdated = true;
                        }
                    }
                }else
                if(!$order->canInvoice())
                {
                    //throw new \Magento\Framework\Exception\LocalizedException(__('The order does not allow an invoice to be created.'));
                }

                /**
                 * Proceed capture if action is triggered
                 */
                if($runCapture)
                {
                    foreach($order->getInvoiceCollection() as $invoice)
                    {
                        if ($this->_scopeConfig->getValue('iksanika_ordermanage/capture/case') != \Magento\Sales\Model\Order\Invoice::NOT_CAPTURE)
                        {

                            if($invoice->canCapture())
                            {
                                $invoice->setRequestedCaptureCase($this->_scopeConfig->getValue('iksanika_ordermanage/capture/case'));
                                $invoice->capture();
                                $invoice->getOrder()->setIsInProcess(true);
                                $transact = $this->_transactionFactory->create()->addObject($invoice)->addObject($invoice->getOrder());
                                $transact->save();
                                $isUpdated = true;
                            }
                        }
                    }
                }

                /*
                 * Ship order and/or send shipment email if reqired
                 */
                if($runShip)
                {
                    if($order->canShip() && !$runSendEmail)
                    {
                        $isUpdated = $this->proceedOrderShipment($order, $itemId, $runNotify) ? true : $isUpdated;
                    }

                    if($runSendEmail)
                    {
                        $shipments  =   $order->getShipmentsCollection();
                        $shipment   =   $shipments->getFirstItem();
                        if($shipment->getId())
                        {
                            $shipment->setCustomerNoteNotify(true);
                            $this->_shipmentSender->send($shipment);
                            $shipment->save();
                            $isUpdated = true;
                        }
                    }
                }


                /**
                 ** Set appropriate order state/status based on triggered actions
                 **/

                /**
                 * Uncancel order if it has Canceled order status
                 */
                if($runSetProcessing && $order->getStatus() == \Magento\Sales\Model\Order::STATE_CANCELED)
                {
                    $order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                    $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
                    $order->save();

                    foreach($order->getAllItems() as $orderedItem)
                    {
                        $orderedItem->setQtyCanceled(0);
                        $orderedItem->save();
                    }
                    $isUpdated = true;
                }

                $newStatus = $order->getStatus();
                if($runComplete)
                {
                    if($statusComplete == 'default')
                    {
                        $newStatus = \Magento\Sales\Model\Order::STATE_COMPLETE;
                        $isUpdated = true;
                    }else
                    if(($order->getStatus() !== $statusComplete) && ($statusComplete !== 'default'))
                    {
                        $newStatus = $statusComplete;
                        $isUpdated = true;
                    }
                }else
                if($runShip && ($order->getStatus() !== $statusShip) && ($statusShip !== 'default'))
                {
                    $newStatus = $statusShip;
                }else
                if($runInvoice && ($order->getStatus() !== $statusInvoice) && ($statusInvoice !== 'default'))
                {
                    $newStatus = $statusInvoice;
                }else
                if($runSetStatus && !empty($newOrderStatus))
                {
                    $newStatus = $newOrderStatus;
                    $isUpdated = true;
                }
                if($newStatus != $order->getStatus())
                {
                    $states = $this->_orderStatuses->getStates();
                    foreach($states as $stateCode => $stateTitle)
                    {
                        $stateStatuses = $this->_orderStatuses->getStateStatuses($stateCode, false);
                        foreach($stateStatuses as $stateStatus)
                        {
                            if($stateStatus == $newStatus)
                            {
                                $order->setState($stateCode);
                                break;
                            }
                        }
                    }
                    $order->setStatus($newStatus);
                    $order->addStatusHistoryComment('', $order->getStatus())->setIsCustomerNotified(0);
                    $order->save();
                }

                /*
                 * Send order email
                 */
                if($runSendOrderEmail)
                {
                    $this->_orderSender->send($order);
                    $isUpdated = true;
                }

                if($isUpdated)
                {
                    $updatedAmount++;
                }
            }catch (\Magento\Framework\Exception\LocalizedException $e)
            {
                $this->messageManager->addErrorMessage('Exception (Order # ' . ((isset($order) && $order && $order->getIncrementId()) ? $order->getIncrementId() : $orderId) . '): ' . $e->getMessage());
            }catch (\Exception $e)
            {
                $this->messageManager->addErrorMessage('Exception (Order # ' . ((isset($order) && $order && $order->getIncrementId()) ? $order->getIncrementId() : $orderId) . '): ' . $e->getMessage());
            }
        }

        /**
         * Send invoice, packing slips and shipping labels if actions triggered
         */
        if($runPrint && $runInvoice)
        {
            $this->messageManager->addComplexNoticeMessage('messageHtmlNotice', ['content' => (string)
                __('<a href="%1" target="_blank">Print the invoice</a> in PDF file format for processed orders.',
                    $this->_helperBackend->getUrl('ordermanage/*/defaultMassPdfinvoices', ['order_ids' => implode(",", $orderIds), 'pr'=> 1]))]
            );
        }
        if($runPrint && $runShip)
        {
            $this->messageManager->addComplexNoticeMessage('messageHtmlNotice', ['content' => (string)
                __('<a href="%1" target="_blank">Print the placking slips</a> in PDF file format for processed orders.',
                    $this->_helperBackend->getUrl('ordermanage/*/defaultMassPdfshipments', ['order_ids' => implode(",", $orderIds), 'pr'=> 1]))]
            );
            $this->messageManager->addComplexNoticeMessage('messageHtmlNotice', ['content' => (string)
                __('<a href="%1" target="_blank">Print the shipping labels</a> in PDF file format for processed orders.',
                    $this->_helperBackend->getUrl('ordermanage/*/defaultMassPrintShippingLabel', ['order_ids' => implode(",", $orderIds), 'pr'=> 1]))]
            );
        }
        $this->messageManager->addSuccessMessage(__('Total of %1 order(s) were proceeded.', $updatedAmount));
        return true;
    }

    /**
     * @param $order \Magento\Sales\Model\Order
     * @param $runNotify
     * @param $callCapture
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function proceedOrderInvoice(&$order, $runNotify, $callCapture)
    {
        /** @var $invoice \Magento\Sales\Model\Order\Invoice */
        $invoice = $order->prepareInvoice();

        if(!$invoice)
        {
            //throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t save the invoice for order (entity_id: %1) right now.', $order->getId()));
            $this->messageManager->addErrorMessage('(Order # ' . ((isset($order) && $order && $order->getIncrementId()) ? $order->getIncrementId() : $order->getId()) . '): ' . __('We can\'t save the invoice for order (entity_id: %1) right now.', $order->getId()));
            return false;
        }

        if(!$invoice->getTotalQty())
        {
            //throw new \Magento\Framework\Exception\LocalizedException(__('You can\'t create an invoice without products for order (entity_id: %1).', $order->getId()));
            $this->messageManager->addErrorMessage('(Order # ' . ((isset($order) && $order && $order->getIncrementId()) ? $order->getIncrementId() : $order->getId()) . '): ' . __('You can\'t create an invoice without products for order (entity_id: %1).', $order->getId()));
            return false;
        }

        if($callCapture && ($this->_scopeConfig->getValue('iksanika_ordermanage/capture/case') != \Magento\Sales\Model\Order\Invoice::NOT_CAPTURE))
        {
            $invoice->setRequestedCaptureCase($this->_scopeConfig->getValue('iksanika_ordermanage/capture/case'));
        }
        $invoice->register();
        $invoice->setCustomerNoteNotify($runNotify);
        $invoice->getOrder()->setIsInProcess(true);
        $transact = $this->_transactionFactory->create()->addObject($invoice)->addObject($invoice->getOrder());
        $transact->save();

        if($runNotify)
        {
            $this->_invoiceSender->send($invoice);
        }
        return true;
    }

    /**
     * @param $order \Magento\Sales\Model\Order
     * @param $itemId
     * @param $runNotify
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function proceedOrderShipment(&$order, $itemId, $runNotify)
    {
        if(!$order->canShip())
        {
            //throw new \Magento\Framework\Exception\LocalizedException(__('Shipment for order (entity_id: %1) can\'t be proceeded right now.', $order->getId()));
            $this->messageManager->addErrorMessage( __('Shipment for order (entity_id: %1) can\'t be proceeded right now.', $order->getId()));
            return false;
        }

        // Compatibility enhancements with M2.2.2+ as additional constrain added in magento-sales module in Shipment Factory -> create ()
        $items = [];
        foreach($order->getAllItems() as $orderItem)
        {
            $items[$orderItem->getId()] = $orderItem->getQtyOrdered();
        }
        //

        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        //$shipment = $this->_shipmentFactory->create($order); // before compatibility enhacement with M2.2.2+
        $shipment = $this->_shipmentFactory->create($order, $items); // compatibility enhancements with M2.2.2+
        $shipment->register();
        $shipment->setCustomerNoteNotify($runNotify);
        $shipment->getOrder()->setIsInProcess(true);

        /**
         * Recognize and add Tracking number
         */
        $trackNumber            =   $this->getRequest()->getParam('shipping_tracking_number');
        $trackNumberCarrierCode =   $this->getRequest()->getParam('shipping_tracking_number_carrier');

        $trackItem = array(
            'carrier_code'  =>  $this->_scopeConfig->getValue('iksanika_ordermanage/ship/carrier_code'),
            'title'         =>  ($this->_scopeConfig->getValue('iksanika_ordermanage/ship/carrier_code') == 'custom') ? $this->_scopeConfig->getValue('iksanika_ordermanage/ship/carrier_title') : $this->_scopeConfig->getValue("carriers/".$this->_scopeConfig->getValue('iksanika_ordermanage/ship/carrier_code')."/title"),
            'number'        =>  ' ',
        );
        if($trackNumberCarrierCode)
        {
            $trackItem = array(
                'carrier_code' => $trackNumberCarrierCode[$itemId],
                'title' => ($trackNumberCarrierCode[$itemId] == 'custom') ? $this->_scopeConfig->getValue('iksanika_ordermanage/ship/carrier_title') : $this->_scopeConfig->getValue("carriers/".$trackNumberCarrierCode[$itemId]."/title"),
                'number' => ($trackNumber && $trackNumber[$itemId] ? $trackNumber[$itemId] : ' '),
            );
        }
        $shipment->addTrack($this->_trackFactory->create()->addData($trackItem));
        $transact = $this->_transactionFactory->create()->addObject($shipment)->addObject($shipment->getOrder());
        $transact->save();

        if($runNotify)
        {
            $this->_shipmentSender->send($shipment);
        }

        return true;
    }

}