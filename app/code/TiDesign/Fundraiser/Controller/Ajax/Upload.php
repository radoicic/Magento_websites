<?php

namespace TiDesign\Fundraiser\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use TiDesign\Fundraiser\Model\ThemeCustomer\File\UploadDraftImageProcessor;

class Upload extends Action
{
    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param UploadDraftImageProcessor $uploadDraftImageProcessor
     */
    public function __construct(
        Context $context,
        protected \Magento\Customer\Model\Session $customerSession,
        protected UploadDraftImageProcessor $uploadDraftImageProcessor
    ) {
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $jsonResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $fileName = $_FILES['filepath']['name'] ?? null;
        $themeId = $this->getRequest()->getParam('theme_id');
        $customerId = $this->customerSession->getCustomerId();
        $selector = $this->getRequest()->getParam('selector');
        if ($fileName && $themeId && $customerId && $selector) {
            try {
                $imageUrl = $this->uploadDraftImageProcessor->execute('filepath', $themeId, $customerId, $selector);
                return $jsonResult->setData(['success' => true, 'image_url' => $imageUrl]);
            } catch (\Exception $e) {
                return $jsonResult->setData(['success' => false, 'message' => $e->getMessage()]);
            }
        } else {
            return $jsonResult->setData(['success' => false, 'message' => "File not found!"]);
        }
    }
}
