<?php

namespace TiDesign\Fundraiser\Controller\Form;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use TiDesign\Fundraiser\Helper\Config as ConfigHelper;

class Landing implements HttpGetActionInterface
{
    /**
     * @param ResultFactory $resultFactory
     * @param ConfigHelper $configHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        protected ResultFactory $resultFactory,
        protected ConfigHelper $configHelper,
        protected StoreManagerInterface $storeManager
    ) {
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->configHelper->isEnabled()) {
            $baseUrl = $this->storeManager->getStore()->getBaseUrl();
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($baseUrl);
        }
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
