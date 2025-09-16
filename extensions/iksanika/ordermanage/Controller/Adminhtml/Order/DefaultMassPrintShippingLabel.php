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
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;

class DefaultMassPrintShippingLabel extends \Magento\Sales\Controller\Adminhtml\Order
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
        
        ShipmentCollectionFactory $shipmentCollectionFactory,
        \Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,

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
        $this->fileFactory = $fileFactory;
        
        $this->dateTime = $dateTime;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->labelGenerator = $labelGenerator;
    }
    
    /**
     * Delete order(s) action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $orderIds = $this->getRequest()->getParam('order_ids');
        if($this->getRequest()->getParam('pr') !== null)
        {
            $orderIds = explode(',', $orderIds);
        }

        $labelsContent = [];
        $shipments = $this->shipmentCollectionFactory->create()->setOrderFilter(['in' => $orderIds]);
        
        if ($shipments->getSize()) {
            /** @var \Magento\Sales\Model\Order\Shipment $shipment */
            foreach ($shipments as $shipment) {
                $labelContent = $shipment->getShippingLabel();
                if ($labelContent) {
                    $labelsContent[] = $labelContent;
                }
            }
        }
        
        if (!empty($labelsContent)) {
            $outputPdf = $this->labelGenerator->combineLabelsPdf($labelsContent);
            return $this->fileFactory->create(
                'ShippingLabels.pdf',
                $outputPdf->render(),
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        }
        
        $this->messageManager->addError(__('There are no shipping labels related to selected orders.'));
        
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
        return $this->_authorization->isAllowed('Iksanika_Ordermanage::ma_print_shipping_lables');
    }
}