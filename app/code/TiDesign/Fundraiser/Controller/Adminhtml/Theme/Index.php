<?php

namespace TiDesign\Fundraiser\Controller\Adminhtml\Theme;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'TiDesign_Fundraiser::theme';

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
            ->addBreadcrumb(__('Themes'), __('Themes'));
        $resultPage->getConfig()->getTitle()->prepend(__('Fundraiser Themes'));

        return $resultPage;
    }
}
