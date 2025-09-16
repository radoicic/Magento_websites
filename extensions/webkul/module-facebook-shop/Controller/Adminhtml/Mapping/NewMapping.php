<?php
/**
 * Webkul  Controller.
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
use Magento\Framework\Controller\ResultFactory;

class NewMapping extends \Magento\Backend\App\Action
{
    /**
     * @var \Webkul\FacebookShop\Model\MappingFactory
     */
    private $mappingFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
 
  /**
   * @param \Magento\Backend\App\Action\Context $context
   * @param \Magento\Framework\Registry $registry
   * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
   * @param \Webkul\FacebookShop\Model\MappingFactory $mappingFactory
   */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Webkul\FacebookShop\Model\MappingFactory $mappingFactory
    ) {
        $this->_backendSession = $context->getSession();
        $this->_registry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->mappingFactory = $mappingFactory;
        parent::__construct($context);
    }
 
    /**
     * Mapped Grid List page.
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('id');
        $mapData = $this->mappingFactory->create();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        if ($rowId) {
            $mapData = $mapData->load($rowId);
            $rowTitle = $mapData->getTitle();
            if (!$mapData->getEntityId()) {
                $this->messageManager->addError(__('row data no longer exist.'));
                return;
            }
        }
        $data = $this->_backendSession->getFormData(true);
       
        if (!empty($data)) {
            $mapData->setData($data);
        }
        $this->_registry->register('facebookshop_mapping', $mapData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('Edit Mapping').$rowTitle : __('Add Mapping');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
    
    /**
     * check allowed role
     *
     * @return void
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_FacebookShop::mapping');
    }
}
