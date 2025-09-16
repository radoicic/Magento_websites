<?php

namespace TiDesign\Fundraiser\Controller\Adminhtml\Theme;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use TiDesign\Fundraiser\Model\Theme\GetByThemeId;
use TiDesign\Fundraiser\Model\Theme\SaveTheme;

class Save extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'TiDesign_Fundraiser::theme';

    /**
     * @param Context $context
     * @param GetByThemeId $getByThemeId
     * @param SaveTheme $saveTheme
     */
    public function __construct(
        Context $context,
        protected GetByThemeId $getByThemeId,
        protected SaveTheme $saveTheme

    ) {
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $themeId = $this->getRequest()->getParam('theme_id');
        $categoryId = $this->getRequest()->getParam('category_id');
        if (!$themeId) {
            $this->messageManager->addErrorMessage(__('Theme ID does not exist'));
            return $result->setPath('*/*');
        }
        $theme = $this->getByThemeId->execute($themeId);
        if (!$theme->getId()) {
            $this->messageManager->addErrorMessage(__('Theme ID does not exist'));
            return $result->setPath('*/*');
        }
        $theme->setCategoryId($categoryId);
        $this->saveTheme->execute($theme);
        $this->messageManager->addSuccessMessage(__('Theme ID: %1 was saved successfully!', $themeId));
        return $result->setPath('*/*/edit', ['theme_id' => $themeId]);
    }
}
