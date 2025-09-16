<?php

namespace TiDesign\Fundraiser\Controller\Form;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use TiDesign\Fundraiser\Model\Customer\IsCurrentCustomerEligibleAccess;

abstract class AccessibleAction
{
    /**
     * @param ResultFactory $resultFactory
     * @param IsCurrentCustomerEligibleAccess $isCurrentCustomerEligibleAccess
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        protected ResultFactory $resultFactory,
        protected IsCurrentCustomerEligibleAccess $isCurrentCustomerEligibleAccess,
        protected ManagerInterface $messageManager
    ) {
    }

    /**
     * Execute action based on request and return result
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->isCurrentCustomerEligibleAccess->validate()) {
            $this->messageManager->addErrorMessage(__('You are not eligible to access the template page. Please create an account or login by an account with access.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('*/*/landing');
        }
        return $this->performAction();
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    protected function performAction()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
