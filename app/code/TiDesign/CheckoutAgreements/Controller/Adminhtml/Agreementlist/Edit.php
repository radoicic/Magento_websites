<?php
/**
 * Copyright © TiDesign All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace TiDesign\CheckoutAgreements\Controller\Adminhtml\Agreementlist;

class Edit extends \TiDesign\CheckoutAgreements\Controller\Adminhtml\Agreementlist
{

    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('agreement_id');
        $model = $this->_objectManager->create(\TiDesign\CheckoutAgreements\Model\Agreementlist::class);
        
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Agreement no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('tidesign_consignment_checkoutagreements_grid', $model);
        
        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Agreement') : __('New'),
            $id ? __('Edit Agreement') : __('New')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Agreementlists'));
		$resultPage->setActiveMenu('TiDesign_Magento_CheckoutAgreements::agreementlist');
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit Agreement %1', $model->getId()) : __('New'));
        return $resultPage;
    }
}

