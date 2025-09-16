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
namespace Webkul\FacebookShop\Controller\Adminhtml\CatalogFeed;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var Webkul\FacebookShop\Helper\Data
     */
    protected $fbShopHelper;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Webkul\FacebookShop\Helper\Data $fbShopHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\FacebookShop\Helper\Data $fbShopHelper
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->fbShopHelper = $fbShopHelper;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Webkul_FacebookShop::catalogfeed');
        $resultPage->getConfig()->getTitle()->prepend(__('Catalog Feed Csv Logs'));
        return $resultPage;
    }

    /**
     * Check for is allowed.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_FacebookShop::catalogfeed');
    }
}
