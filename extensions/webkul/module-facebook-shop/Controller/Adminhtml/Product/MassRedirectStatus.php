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
namespace Webkul\FacebookShop\Controller\Adminhtml\Product;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class MassRedirectStatus extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Webkul\FacebookShop\Helper\Data $fbHelper
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->fbHelper = $fbHelper;
        parent::__construct($context);
    }

    /**
     * execute
     *
     * @return void
     */
    public function execute()
    {
        try {
            $fbHelper = $this->fbHelper;
            $mappingIds = [];
            $postParams = $this->getRequest()->getParams();
            $fbProductRedirect = 1;
            if (!empty($postParams['status'])) {
                if ($postParams['status'] == 2) {
                    $fbProductRedirect = 0;
                }
            }
            $defaultGpc = $fbHelper->getConfigValue('facebook_shop_product_configuration', 'default_gpc');
            $defaultBrand = $fbHelper->getConfigValue('facebook_shop_product_configuration', 'default_brand');
            $collection = $this->_filter->getCollection($this->_collectionFactory->create()->addAttributeToSelect('*'));
            foreach ($collection as $product) {
                if (empty($product->getFbProductBrand())) {
                    $product->setFbProductBrand($defaultBrand);
                }
                if (empty($product->getGoogleProductCategory())) {
                    $product->setGoogleProductCategory($defaultGpc);
                }
                $product->setRedirectToProduct($fbProductRedirect);
                $product->save();
            }
            $message = 'Enabled';
            if (!empty($postParams['status']) && $postParams['status']==2) {
                $message = 'Disabled';
            }
            $this->messageManager->addSuccess(__('Product(s) Page Redirect '.$message. ' for Facebook Products'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('catalog/product/index');
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect->setPath('catalog/product/index');
        }
    }
}
