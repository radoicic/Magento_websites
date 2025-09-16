<?php

namespace TiDesign\Fundraiser\Controller\Adminhtml\Theme\Customer;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use TiDesign\Fundraiser\Model\ThemeCustomer\SaveTheme;
use TiDesign\Fundraiser\Model\ThemeCustomer\GetThemeCustomerById;
use TiDesign\Fundraiser\Model\ThemeCustomer\OptionSource\Status;

class Save extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'TiDesign_Fundraiser::theme_customer';

    /**
     * @param Context $context
     * @param GetThemeCustomerById $getThemeCustomerById
     * @param SaveTheme $saveTheme
     */
    public function __construct(
        Context $context,
        protected GetThemeCustomerById $getThemeCustomerById,
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
        $id = $this->getRequest()->getParam('id');
        $categoryId = $this->getRequest()->getParam('category_id');
        $status = $this->getRequest()->getParam('status');
        if (!$id) {
            $this->messageManager->addErrorMessage(__('Campaign does not exist'));
            return $result->setPath('*/*');
        }
        $theme = $this->getThemeCustomerById->execute($id);
        if (!$theme->getId()) {
            $this->messageManager->addErrorMessage(__('Campaign does not exist'));
            return $result->setPath('*/*');
        }
        if (is_array($categoryId)) {
            $categoryId = implode(",", $categoryId ?: []);
        }
        $theme->setCategoryId($categoryId ?: null);
        $theme->setStatus($status ?: Status::PENDING_APPROVAL);
        $this->saveTheme->execute($theme);
        $this->messageManager->addSuccessMessage(__('Campaign ID: %1 was saved successfully!', $id));
        return $result->setPath('*/*/edit', ['id' => $id]);
    }
}
