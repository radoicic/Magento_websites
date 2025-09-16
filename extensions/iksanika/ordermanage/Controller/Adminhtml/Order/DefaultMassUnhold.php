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
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Config;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class DefaultMassUnhold extends \Magento\Sales\Controller\Adminhtml\Order
{

    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        \Magento\Framework\App\Config $config,
        \Iksanika\Ordermanage\Helper\Data $helper
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_fileFactory = $fileFactory;
        $this->_translateInline = $translateInline;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->resultRawFactory = $resultRawFactory;
        parent::__construct($context, $coreRegistry, $fileFactory, $translateInline, $resultPageFactory, $resultJsonFactory, $resultLayoutFactory, $resultRawFactory, $orderManagement, $orderRepository, $logger);
        $this->_helperData = $helper;
        $this->_helperData->setScopeConfig($config);
        $this->_scopeConfig = $config;
        $this->orderManagement = $orderManagement;
    }
    
    /**
     * Delete order(s) action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $orderIds = $this->getRequest()->getParam('order_ids');
        
        if(is_array($orderIds)) 
        {
            $orderFactory = new \Magento\Sales\Model\OrderFactory($this->_objectManager);
            try {
                $countUnHoldOrder = 0;
                foreach($orderIds as $itemId => $orderId) 
                {
                    $order = $orderFactory->create();
                    if($orderId) 
                    {
                        try {
                            $order->load($orderId);
                        } catch (\Exception $e) {
                            $this->logger->critical($e);
                        }
                    }
                    // unhold order
                    $order->load($order->getId());
                    if (!$order->canUnhold()) {
                        continue;
                    }
                    $order->unhold();
                    $order->save();
                    $countUnHoldOrder++;
                }
                
                $countNonUnHoldOrder = count($orderIds) - $countUnHoldOrder;

                if ($countNonUnHoldOrder && $countUnHoldOrder) {
                    $this->messageManager->addError(
                        __('%1 order(s) were not released from on hold status.', $countNonUnHoldOrder)
                    );
                } elseif ($countNonUnHoldOrder) {
                    $this->messageManager->addError(__('No order(s) were released from on hold status.'));
                }

                if ($countUnHoldOrder) {
                    $this->messageManager->addSuccess(
                        __('%1 order(s) have been released from on hold status.', $countUnHoldOrder)
                    );
                }
        
//                $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', count($orderIds)));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_getSession()->addException($e, __('Something went wrong while deleting the order(s).'));
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select order(s)').'. '.$this->__('You should select checkboxes for each order row which should be deleted. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('ordermanage/*/', ['_current' => true, '_query' => 'st=1']); //'store' => $storeId]
    }


    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Iksanika_Ordermanage::ma_unhold');
    }
    
}