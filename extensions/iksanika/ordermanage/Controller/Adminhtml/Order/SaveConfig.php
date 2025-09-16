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
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class SaveConfig extends \Magento\Sales\Controller\Adminhtml\Order
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
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        \Magento\Framework\App\Config $config,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Iksanika\Ordermanage\Helper\Data $helper,

        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,

        \Magento\Framework\App\Cache\Manager $cacheManager,
        \Magento\Framework\App\Cache\Type\Config $cacheTypeConfig
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
        $this->_resourceConfig = $resourceConfig;

        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;

        $this->_cacheManager = $cacheManager;
        $this->_cacheTypeConfig = $cacheTypeConfig;
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
    
    protected function saveConfig($pathId, $value, $scope = 'default', $scopeId = 0)
    {
        $this->_resourceConfig->saveConfig($pathId, $value, $scope, $scopeId);
    }

    /**
     * Update product(s) status action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $settingsFields = $this->getRequest()->getParam('settings', array());

//        $this->saveConfig('iksanika_ordermanage/columns/mode', $settingsFields['columns']['mode']);
        $this->saveConfig('iksanika_ordermanage/columns/showcolumns', $settingsFields['columns']['showcolumns']);
        $this->saveConfig('iksanika_ordermanage/columns/hide_status', $settingsFields['columns']['hide_status']);
        
        $this->saveConfig('iksanika_ordermanage/images/width', $settingsFields['images']['width']);
        $this->saveConfig('iksanika_ordermanage/images/height', $settingsFields['images']['height']);
        $this->saveConfig('iksanika_ordermanage/images/scale', $settingsFields['images']['scale']);
        
        $this->saveConfig('iksanika_ordermanage/products/showattr', $settingsFields['products']['showattr']);
        $this->saveConfig('iksanika_ordermanage/products/hideorderitems', $settingsFields['products']['hideorderitems']);
        $this->saveConfig('iksanika_ordermanage/products/includeproducts', $settingsFields['products']['includeproducts']);
        $this->saveConfig('iksanika_ordermanage/products/showproducts', $settingsFields['products']['showproducts']);
        
//        $config->cleanCache();
        $this->_cacheTypeList->cleanType('config');

        $result = array('success' => 1);
        
        $this->getResponse()->representJson(
            $this->_objectManager->create('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
        );
    }

    
}