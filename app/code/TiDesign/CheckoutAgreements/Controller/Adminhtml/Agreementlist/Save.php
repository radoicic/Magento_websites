<?php
/**
 * Copyright © TiDesign All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace TiDesign\CheckoutAgreements\Controller\Adminhtml\Agreementlist;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
	protected $_agreementlistFactory;
    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context 					$context,
		\TiDesign\CheckoutAgreements\Model\AgreementlistFactory 		$agreementlistFactory,
        \Magento\Framework\App\Request\DataPersistorInterface 	$dataPersistor
    ) {
        $this->dataPersistor 			= $dataPersistor;
		$this->_agreementlistFactory 	= $agreementlistFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $postData = (array) $this->getRequest()->getPost();
		//echo "<pre>";print_r($postData);die("</pre>");
		if (!empty($postData)) {
			$data = null;
			if (!empty($postData['agreement_id'])) {
				$data['agreement_id']   = $postData['agreement_id']; 
			}			
			$data['store_id'] 			= implode(',',$postData['store_id']);
			$data['customer_group'] 	= implode(',',$postData['customer_group']);			
			$data['name']				= $postData['name'];
			$data['content']			= $postData['content'];
			$data['is_active']			= $postData['is_active'];
			$data['checkbox_text']		= $postData['checkbox_text'];
			
			$model = $this->_agreementlistFactory->create();
			$model->setData($data);
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Agreement.'));
                $this->dataPersistor->clear('tidesign_consignment_checkoutagreements_grid');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['agreement_id' => $model->getAgreementId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Agreement.'));
            }
        
            $this->dataPersistor->set('tidesign_consignment_checkoutagreements_grid', $data);
            return $resultRedirect->setPath('*/*/edit', ['agreement_id' => $model->getAgreementId()]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}

