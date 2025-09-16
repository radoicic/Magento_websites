<?php

namespace TiDesign\Fundraiser\Controller\Adminhtml\Theme\Customer;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'TiDesign_Fundraiser::theme_customer';

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('TiDesign_Fundraiser::fundraiser')
            ->addBreadcrumb(__('Fundraiser'), __('Fundraiser'))
            ->addBreadcrumb(__('Campaigns'), __('Campaigns'));
        $resultPage->getConfig()->getTitle()->prepend(__('Fundraiser Campaigns'));

        return $resultPage;
    }
}
