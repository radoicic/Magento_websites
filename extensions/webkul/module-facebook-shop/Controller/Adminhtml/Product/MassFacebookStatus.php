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

class MassFacebookStatus extends \Magento\Backend\App\Action
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
        $fbHelper = $this->fbHelper;
        $mappingIds = [];
        $postParams = $this->getRequest()->getParams();
        $fbProductStatus = 1;
        if (!empty($postParams['status'])) {
            if ($postParams['status'] == 2) {
                $fbProductStatus = 0;
            }
        }
        try {
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
                $product->setIsFacebookProduct($fbProductStatus);
                $product->save();
            }
            $message = 'Enabled';
            if (!empty($postParams['status']) && $postParams['status']==2) {
                $message = 'Disabled';
            }
            $this->messageManager->addSuccess(__('Product(s) '.$message. ' for Facebook'));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
       
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('catalog/product/index');
    }
}
