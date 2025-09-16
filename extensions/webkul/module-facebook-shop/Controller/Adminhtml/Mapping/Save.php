<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_FacebookShop
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\FacebookShop\Controller\Adminhtml\Mapping;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\LocalizedException;
use Webkul\FacebookShop\Model\MappingFactory;

class Save extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\Page
     */
    protected $resultPage;
    /**
     * @var Webkul\FacebookShop\Model\MappingFactory
     */
    protected $mappingCollection;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

   /**
    * @param Context $context
    * @param PageFactory $resultPageFactory
    * @param \Webkul\FacebookShop\Model\MappingFactory $mappingFactory
    * @param \Magento\Framework\Message\ManagerInterface $messageManager
    * @param \Webkul\FacebookShop\Helper\Data $fbShopHelper
    */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\FacebookShop\Model\MappingFactory $mappingFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Webkul\FacebookShop\Helper\Data $fbShopHelper
    ) {
        parent::__construct($context);
        $this->mappingFactory = $mappingFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->messageManager = $messageManager;
        $this->fbShopHelper = $fbShopHelper;
    }

    /**
     * Save Mapping controller
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->getRequest()->getParams()) {
            $this->messageManager->addError(__("Something went wrong"));
            return $resultRedirect->setPath('*/*/');
        }
        try {
            $postData = $this->getRequest()->getParams();
            if (empty($postData['fb_attribute_id']) && empty($postData['product_attribute_id'])) {
                $this->messageManager->addError(__("Invalid Data provided"));
                return $resultRedirect->setPath('*/*/');
            }
            $mappingCollection = $this->mappingFactory->create()->getCollection();
            $mappingCollection->addFieldToFilter('product_attribute_id', ['eq' => $postData['product_attribute_id']]);
            $mappingCollection->addFieldToFilter('fb_attribute_id', ['eq' => $postData['fb_attribute_id']]);
            if (!empty($mappingCollection->getSize())) {
                $this->messageManager->addError(__(
                    "Mapping exist for facebook attribute %1 with product attribute %2",
                    $postData['fb_attribute_id'],
                    $postData['product_attribute_id']
                ));
                return $resultRedirect->setPath('*/*/');
            }
            $fbAttributes = $this->fbShopHelper->getAllFbAttributes();
            if (array_key_exists($postData['fb_attribute_id'], $fbAttributes)) {
                $postData['fb_attribute_name'] = $fbAttributes[$postData['fb_attribute_id']];
            }
            $mappingFactory = $this->mappingFactory->create();
            $mappingFactory->setData($postData);
            $mappingFactory->save();
            $this->messageManager->addSuccess(__("Mapping Successfully Created"));
            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Something went wrong"));
            return $resultRedirect->setPath('*/*/');
        }
    }
}
