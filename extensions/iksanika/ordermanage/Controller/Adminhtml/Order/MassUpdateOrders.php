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
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Config;

class MassUpdateOrders extends \Magento\Sales\Controller\Adminhtml\Order
{

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Psr\Log\LoggerInterface $logger,
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
    }
    
    
    /**
     * Validate batch of products before theirs status will be set
     *
     * @param array $productIds
     * @param int $status
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _validateMassStatus(array $productIds, $status)
    {
        /*
        if ($status == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED) {
            if (!$this->_objectManager->create('Magento\Catalog\Model\Product')->isProductsHasSku($productIds)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Please make sure to define SKU values for all processed products.')
                );
            }
        }
\         */
    }

    /**
     * Update product(s) status action
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
                
                foreach($orderIds as $itemId => $orderId) 
                {
                    $order = $orderFactory->create();
                    if($orderId) 
                    {
                        try {
                            $order->load($orderId);
                            $orderBefore  =   $order;
                        } catch (\Exception $e) {
//                            $order->setTypeId(\Magento\Catalog\Model\Product\Type::DEFAULT_TYPE);
                            $this->logger->critical($e);
                        }
                    }
                    
                    $columnForUpdate = $this->_helperData->getColumnForUpdate();
                    
                    $billingDataFlag = false;
                    $billingAddress = null;
                    $shippingDataFlag = false;
                    $shippingAddress = null;
//echo '<pre>';
//var_dump($columnForUpdate);
                    foreach($columnForUpdate as $columnName)
                    {
                        $columnValuesForUpdate = $this->getRequest()->getParam($columnName);
                        
                        if($columnName == 'billing_firstname' || 
                           $columnName == 'billing_middlename' || 
                           $columnName == 'billing_lastname' || 
                           $columnName == 'billing_company' ||
                           $columnName == 'billing_city' || 
                           $columnName == 'billing_region' || 
                           $columnName == 'billing_postcode' || 
                           $columnName == 'billing_email' || 
                           $columnName == 'billing_telephone' || 
                           $columnName == 'billing_fax' ||
                                
                           $columnName == 'billing_street' ||
                           $columnName == 'billing_country'
                        )
                        {
//echo 'billingDataFlag';
                            $billingDataFlag = true;
                            
                            list($prefix, $fieldName) = explode('_', $columnName);
                            
                            if(!$billingAddress)
                                $billingAddress = $order->getBillingAddress();
                            
                            if($fieldName == 'street')
                                $billingAddress->setStreet($columnValuesForUpdate[$itemId]);
                            else
                            if($fieldName == 'country')
                            {
                                $billingAddress->setCountryId($columnValuesForUpdate[$itemId]);
                            }
                            else
                                $billingAddress->setData($fieldName, $columnValuesForUpdate[$itemId]);
                        }else
                        if($columnName == 'shipping_firstname' || 
                           $columnName == 'shipping_middlename' || 
                           $columnName == 'shipping_lastname' || 
                           $columnName == 'shipping_company' ||
                           $columnName == 'shipping_city' || 
                           $columnName == 'shipping_region' || 
                           $columnName == 'shipping_postcode' || 
                           $columnName == 'shipping_email' || 
                           $columnName == 'shipping_telephone' || 
                           $columnName == 'shipping_fax' ||
                                
                           $columnName == 'shipping_street' ||
                           $columnName == 'shipping_country'
                        )
                        {
//echo 'shippingDataFlag';
                            $shippingDataFlag = true;
                            
                            list($prefix, $fieldName) = explode('_', $columnName);
                            
                            if(!$shippingAddress)
                                $shippingAddress = $order->getShippingAddress();
                            
                            if($fieldName == 'street')
                                $shippingAddress->setStreet($columnValuesForUpdate[$itemId]);
                            else
                            if($fieldName == 'country')
                            {
                                $shippingAddress->setCountryId($columnValuesForUpdate[$itemId]);
                            }
                            else
                                $shippingAddress->setData($fieldName, $columnValuesForUpdate[$itemId]);
                        }
                        
//                        $order->$columnName =  $columnValuesForUpdate[$itemId];
                        $order->setData($columnName, $columnValuesForUpdate[$itemId]);
                        //echo $columnName.' = '.$columnValuesForUpdate.'['.$itemId.'] => '.$columnValuesForUpdate[$itemId].'<br/>';
                    }
                    
                    // save billing address changes if exist
                    if($billingDataFlag)
                        $billingAddress->save();
                    
                    // save shipping address changes if exist
                    if($shippingDataFlag)
                        $shippingAddress->save();
                    
                    // save order changes
                    $order->save();
                }
                $this->messageManager->addSuccess(__('A total of %1 record(s) have been updated.', count($orderIds)));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_getSession()->addException($e, __('Something went wrong while updating the product(s) status.'));
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
//        $this->_redirect('productmanage/*/index', array('_current' => true, '_query' => 'st=1'));
//die('tmpr');
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
        return $this->_authorization->isAllowed('Iksanika_Ordermanage::ma_update');
    }
}