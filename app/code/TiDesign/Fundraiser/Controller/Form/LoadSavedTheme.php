<?php

namespace TiDesign\Fundraiser\Controller\Form;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use TiDesign\Fundraiser\Model\Customer\IsCurrentCustomerEligibleAccess;
use TiDesign\Fundraiser\Model\ThemeCustomer\File\CurrentCustomerImagesService;
use TiDesign\Fundraiser\Model\ThemeCustomer\GetThemeCustomerConfigDataByCurrentCustomerId;

class LoadSavedTheme extends AccessibleAction implements HttpGetActionInterface
{
    /**
     * @param ResultFactory $resultFactory
     * @param IsCurrentCustomerEligibleAccess $isCurrentCustomerEligibleAccess
     * @param ManagerInterface $messageManager
     * @param RequestInterface $request
     * @param GetThemeCustomerConfigDataByCurrentCustomerId $getThemeCustomerConfigDataByCurrentCustomerId
     * @param CurrentCustomerImagesService $currentCustomerImagesService
     */
    public function __construct(
        ResultFactory $resultFactory,
        IsCurrentCustomerEligibleAccess $isCurrentCustomerEligibleAccess,
        ManagerInterface $messageManager,
        protected RequestInterface $request,
        protected GetThemeCustomerConfigDataByCurrentCustomerId $getThemeCustomerConfigDataByCurrentCustomerId,
        protected CurrentCustomerImagesService $currentCustomerImagesService
    ) {
        parent::__construct($resultFactory, $isCurrentCustomerEligibleAccess, $messageManager);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    protected function performAction()
    {
        $themeId = $this->request->getParam('theme_id');
        $customerConfigData = $this->getThemeCustomerConfigDataByCurrentCustomerId->execute($themeId);
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $this->currentCustomerImagesService->copyToDraft($themeId);
        return $result->setData(['theme_customer_config_data' => $customerConfigData]);
    }
}
