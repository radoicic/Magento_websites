<?php

namespace TiDesign\Fundraiser\Controller\Adminhtml\Theme\Customer;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use TiDesign\Fundraiser\Model\ThemeCustomer\GetThemeDocumentCustomerById;

class Edit extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'TiDesign_Fundraiser::theme_customer';

    /**
     * @param Context $context
     * @param GetThemeDocumentCustomerById $getThemeDocumentCustomerById
     */
    public function __construct(
        Context $context,
        protected GetThemeDocumentCustomerById $getThemeDocumentCustomerById
    ) {
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $themeCustomer = $this->getThemeDocumentCustomerById->execute($id);
        if (!$themeCustomer || !$themeCustomer->getId()) {
            $this->messageManager->addErrorMessage(__('Campaign does not exist'));
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('TiDesign_Fundraiser::fundraiser')
            ->addBreadcrumb(__('Fundraiser'), __('Fundraiser'))
            ->addBreadcrumb(__('Campaigns'), __('Campaigns'));

        $themeId = $themeCustomer->getThemeId();
        $customerEmail = $themeCustomer->getCustomerEmail();
        $resultPage->getConfig()->getTitle()->prepend(__('Theme ID %1 - Customer %2', $themeId, $customerEmail));

        return $resultPage;
    }
}
