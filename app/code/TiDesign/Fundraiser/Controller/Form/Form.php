<?php

namespace TiDesign\Fundraiser\Controller\Form;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use TiDesign\Fundraiser\Helper\Config as ConfigHelper;
use TiDesign\Fundraiser\Model\Customer\IsCurrentCustomerEligibleAccess;

class Form extends AccessibleAction implements HttpGetActionInterface
{
    public function __construct(
        ResultFactory $resultFactory,
        IsCurrentCustomerEligibleAccess $isCurrentCustomerEligibleAccess,
        ManagerInterface $messageManager,
        protected ConfigHelper $configHelper,
        protected StoreManagerInterface $storeManager
    ) {
        parent::__construct($resultFactory, $isCurrentCustomerEligibleAccess, $messageManager);
    }

    /**
     * Execute action based on request and return result
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->configHelper->isEnabled()) {
            $baseUrl = $this->storeManager->getStore()->getBaseUrl();
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($baseUrl);
        }
        return parent::execute();
    }
}
