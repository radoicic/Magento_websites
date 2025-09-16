<?php

namespace TiDesign\Fundraiser\Controller\Form;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutFactory;
use TiDesign\Fundraiser\Model\Customer\IsCurrentCustomerEligibleAccess;
use TiDesign\Fundraiser\Model\Theme\GetByThemeId;
use TiDesign\Fundraiser\Model\ThemeCustomer\File\CurrentCustomerImagesService;
use TiDesign\Fundraiser\Model\ThemeCustomer\GetThemeByCurrentCustomerId;
use TiDesign\Fundraiser\Model\ThemeCustomer\GetThemeCustomerConfigDataByCurrentCustomerId;

class LoadTheme extends AccessibleAction implements HttpGetActionInterface
{
    /**
     * @param ResultFactory $resultFactory
     * @param IsCurrentCustomerEligibleAccess $isCurrentCustomerEligibleAccess
     * @param ManagerInterface $messageManager
     * @param RequestInterface $request
     * @param LayoutFactory $layoutFactory
     * @param GetByThemeId $getByThemeId
     * @param GetThemeCustomerConfigDataByCurrentCustomerId $getThemeCustomerConfigDataByCurrentCustomerId
     */
    public function __construct(
        ResultFactory $resultFactory,
        IsCurrentCustomerEligibleAccess $isCurrentCustomerEligibleAccess,
        ManagerInterface $messageManager,
        protected RequestInterface $request,
        protected LayoutFactory $layoutFactory,
        protected GetByThemeId $getByThemeId,
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
        $isLoadSaved = $this->request->getParam('is_load_saved');
        $layout = $this->layoutFactory->create();
        $block = $layout->createBlock(Template::class);
        $block->setTemplate("TiDesign_Fundraiser::themes/$themeId/theme.phtml");
        $html = $block->toHtml();
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $theme = $this->getByThemeId->execute($themeId);
        $themeData = [
            'theme_id' => $themeId,
            'html' => $html,
            'config_data' => $theme->getConfigData()
        ];
        $this->currentCustomerImagesService->deleteDraft($themeId);
        if ($isLoadSaved) {
            $customerConfigData = $this->getThemeCustomerConfigDataByCurrentCustomerId->execute($themeId);
            $themeData['theme_customer_config_data'] = $customerConfigData;
            $this->currentCustomerImagesService->copyToDraft($themeId);
        }
        return $result->setData($themeData);
    }
}
