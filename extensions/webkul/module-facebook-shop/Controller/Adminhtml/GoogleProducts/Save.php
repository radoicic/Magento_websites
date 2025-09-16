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

namespace Webkul\FacebookShop\Controller\Adminhtml\GoogleProducts;

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
     * @var Webkul\FacebookShop\Model\GoogleProductsFactory
     */
    protected $googleProductsFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

   /**
    * @param Context $context
    * @param PageFactory $resultPageFactory
    * @param \Magento\Framework\Message\ManagerInterface $messageManager
    * @param \Webkul\FacebookShop\Helper\Data $fbShopHelper
    * @param \Magento\Catalog\Model\ProductFactory $productFactory
    */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Webkul\FacebookShop\Helper\Data $fbShopHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->messageManager = $messageManager;
        $this->fbShopHelper = $fbShopHelper;
        $this->productFactory = $productFactory;
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
            $productsArray = [];
            $postData = $this->getRequest()->getParams();
            if (empty($postData['facebookshop']['gpc'])) {
                $this->messageManager->addError(__("Google Product Category cannot be empty"));
                return $resultRedirect->setPath('*/*/');
            }
            if (strpos($postData['facebookshop']['gpc'], '<script>') !== false) {
                $this->messageManager->addError(__("Invalid Data Provided"));
                return $resultRedirect->setPath('*/*/');
            }
            if (empty($postData['gpc_products'])) {
                $this->messageManager->addError(__("Please select at least one product to assign to category"));
                return $resultRedirect->setPath('*/*/');
            }
            $gpc = $postData['facebookshop']['gpc'];
            $productModel = $this->productFactory->create()->getCollection();
            $productModel->addAttributeToSelect('*');
            $productModel->addAttributeToFilter('is_facebook_product', 1);
            $searchForValue = ',';
            $gpcProductsStr = $postData['gpc_products'];
            if (strpos($gpcProductsStr, $searchForValue) !== false) {
                $productsArray =  explode(',', $gpcProductsStr);
            } else {
                $productsArray[] = $gpcProductsStr;
            }
            
            if (!empty($productsArray)) {
                if (!in_array('on', $productsArray)) {
                    $productModel->addFieldToFilter('entity_id', ['in' => $productsArray]);
                }
                foreach ($productModel as $product) {
                    $product->setGoogleProductCategory($gpc);
                    $product->save();
                }
                $this->messageManager->addSuccess(__("Category has been successfully assigned to the products"));
                return $resultRedirect->setPath('*/*/');
            }

        } catch (\Exception $e) {
            $this->messageManager->addError(__("Something went wrong"));
            return $resultRedirect->setPath('*/*/');
        }
    }
}
