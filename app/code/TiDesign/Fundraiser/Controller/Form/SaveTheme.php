<?php

namespace TiDesign\Fundraiser\Controller\Form;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use TiDesign\Fundraiser\Model\Customer\IsCurrentCustomerEligibleAccess;
use TiDesign\Fundraiser\Model\Theme\GetByThemeId;
use TiDesign\Fundraiser\Model\ThemeCustomer\File\CurrentCustomerImagesService;
use TiDesign\Fundraiser\Model\ThemeCustomer\GetThemeByCurrentCustomerId;

class SaveTheme extends AccessibleAction implements HttpPostActionInterface
{
    /**
     * @param ResultFactory $resultFactory
     * @param IsCurrentCustomerEligibleAccess $isCurrentCustomerEligibleAccess
     * @param ManagerInterface $messageManager
     * @param RequestInterface $request
     * @param GetByThemeId $getByThemeId
     * @param GetThemeByCurrentCustomerId $getThemeByCurrentCustomerId
     * @param \TiDesign\Fundraiser\Model\ThemeCustomer\SaveTheme $saveTheme
     * @param CurrentCustomerImagesService $currentCustomerImagesService
     */
    public function __construct(
        ResultFactory $resultFactory,
        IsCurrentCustomerEligibleAccess $isCurrentCustomerEligibleAccess,
        ManagerInterface $messageManager,
        protected RequestInterface $request,
        protected GetByThemeId $getByThemeId,
        protected GetThemeByCurrentCustomerId $getThemeByCurrentCustomerId,
        protected \TiDesign\Fundraiser\Model\ThemeCustomer\SaveTheme $saveTheme,
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
        $data = $this->request->getParam('data');
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        if (!$this->getByThemeId->execute($themeId)->getId()) {
            return $result->setData(['success' => false]);
        }
        $theme = $this->getThemeByCurrentCustomerId->execute($themeId);
        $theme->setConfigData($data);
        $this->saveTheme->execute($theme);
        $this->currentCustomerImagesService->publish($themeId);
        return $result->setData(['success' => true]);
    }
}
