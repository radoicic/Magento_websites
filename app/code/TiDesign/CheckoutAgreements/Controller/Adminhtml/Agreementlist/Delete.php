<?php
/**
 * Copyright © TiDesign All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace TiDesign\CheckoutAgreements\Controller\Adminhtml\Agreementlist;

class Delete extends \TiDesign\CheckoutAgreements\Controller\Adminhtml\Agreementlist
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('agreement_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\TiDesign\CheckoutAgreements\Model\Agreementlist::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Agreementlist.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['agreement_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Agreementlist to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}

