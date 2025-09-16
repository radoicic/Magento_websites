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
use TiDesign\Fundraiser\Model\ThemeCustomer\GetProductsToPrintByCurrentCustomerId;
use TiDesign\Fundraiser\Model\ThemeCustomer\GetThemeCustomerConfigDataByCurrentCustomerId;
use TiDesign\Fundraiser\ViewModel\Product as ProductViewModel;

class PrintAction extends LoadTheme implements HttpGetActionInterface
{
    /**
     * @param ResultFactory $resultFactory
     * @param IsCurrentCustomerEligibleAccess $isCurrentCustomerEligibleAccess
     * @param ManagerInterface $messageManager
     * @param RequestInterface $request
     * @param LayoutFactory $layoutFactory
     * @param GetByThemeId $getByThemeId
     * @param GetThemeCustomerConfigDataByCurrentCustomerId $getThemeCustomerConfigDataByCurrentCustomerId
     * @param CurrentCustomerImagesService $currentCustomerImagesService
     * @param GetProductsToPrintByCurrentCustomerId $getProductsToPrintByCurrentCustomerId
     * @param ProductViewModel $productViewModel
     */
    public function __construct(
        ResultFactory $resultFactory,
        IsCurrentCustomerEligibleAccess $isCurrentCustomerEligibleAccess,
        ManagerInterface $messageManager,
        RequestInterface $request,
        LayoutFactory $layoutFactory,
        GetByThemeId $getByThemeId,
        GetThemeCustomerConfigDataByCurrentCustomerId $getThemeCustomerConfigDataByCurrentCustomerId,
        CurrentCustomerImagesService $currentCustomerImagesService,
        protected GetProductsToPrintByCurrentCustomerId $getProductsToPrintByCurrentCustomerId,
        protected ProductViewModel $productViewModel
    ) {
        parent::__construct(
            $resultFactory,
            $isCurrentCustomerEligibleAccess,
            $messageManager,
            $request,
            $layoutFactory,
            $getByThemeId,
            $getThemeCustomerConfigDataByCurrentCustomerId,
            $currentCustomerImagesService
        );
    }

    protected function performAction()
    {
        $themeId = $this->request->getParam('theme_id');
        $pageType = $this->request->getParam('page_type');
        if (!$themeId || !$pageType) {
            return "";
        }
        $layout = $this->layoutFactory->create();
        $pageData = $this->getPageData();
        $html = [];
        foreach ($pageData as $data) {
            $block = $layout->createBlock(Template::class);
            $data['product_view_model'] = $this->productViewModel;
            $block->setData($data);
            $block->setTemplate("TiDesign_Fundraiser::themes/$themeId/$pageType.phtml");
            $html[] = $block->toHtml();
        }
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $printData = [
            'theme_id' => $themeId,
            'html' => $html
        ];
        return $result->setData($printData);
    }

    /**
     * @return array
     */
    private function getPageData()
    {
        $themeId = $this->request->getParam('theme_id');
        $pageType = $this->request->getParam('page_type');
        if ($pageType === 'products-page') {
            return $this->getProductsPageData();
        } else {
            $themeCustomerConfigData = $this->getThemeCustomerConfigDataByCurrentCustomerId->execute($themeId);
            $result = [['theme_customer_config_data' => $themeCustomerConfigData]];
        }
        return $result;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getProductsPageData()
    {
        $themeId = $this->request->getParam('theme_id');
        $themeCustomerConfigData = $this->getThemeCustomerConfigDataByCurrentCustomerId->execute($themeId);
        $productsPerPage = $this->getProductsToPrintByCurrentCustomerId->execute($themeId);
        $result = [];
        $currentIndex = -1;
        foreach ($productsPerPage as $products) {
            $currentIndex++;
            $result[] = [
                'page_num' => $currentIndex + 1,
                'theme_customer_config_data' => $themeCustomerConfigData,
                'products' => $products,
                'is_last_page' => false,
            ];
        }
        if ($currentIndex >= 0) {
            $lastPage = &$result[$currentIndex];
            $totalPage = $currentIndex + 1;
            $maxProductsLastPage = max(0, 6 - ceil($totalPage / 4));
            if (count($lastPage['products']) > $maxProductsLastPage) {
                $result[] = [
                    'page_num' => $currentIndex + 2,
                    'theme_customer_config_data' => $themeCustomerConfigData,
                    'products' => [],
                    'is_last_page' => true
                ];
            } else {
                $lastPage['is_last_page'] = true;
            }
        }
        return $result;
    }
}
