<?php

namespace TiDesign\Fundraiser\Controller\Adminhtml\Theme;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use TiDesign\Fundraiser\Model\Theme\CurrentThemeIdStorage;
use TiDesign\Fundraiser\Model\Theme\GetByThemeId;

class Edit extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'TiDesign_Fundraiser::theme';

    /**
     * @param Context $context
     * @param GetByThemeId $getByThemeId
     * @param CurrentThemeIdStorage $currentThemeIdStorage
     */
    public function __construct(
        Context $context,
        protected GetByThemeId $getByThemeId,
        protected CurrentThemeIdStorage $currentThemeIdStorage
    ) {
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $themeId = $this->getRequest()->getParam('theme_id');
        $theme = $this->getByThemeId->execute($themeId);
        if ($theme->getId()) {
            $this->currentThemeIdStorage->setThemeId($themeId);
        }
        if (!$themeId) {
            $this->messageManager->addErrorMessage(__('Theme ID does not exist'));
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('TiDesign_Fundraiser::fundraiser')
            ->addBreadcrumb(__('Fundraiser'), __('Fundraiser'))
            ->addBreadcrumb(__('Themes'), __('Themes'));

        $resultPage->getConfig()->getTitle()->prepend(__('Theme ID: %1', $themeId));

        return $resultPage;
    }
}
