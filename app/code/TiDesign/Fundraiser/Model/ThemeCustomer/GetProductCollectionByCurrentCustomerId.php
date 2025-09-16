<?php

namespace TiDesign\Fundraiser\Model\ThemeCustomer;

use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Collection\SalableProcessor;
use Magento\Store\Model\StoreManagerInterface;
use TiDesign\Fundraiser\Helper\Config as ConfigHelper;

class GetProductCollectionByCurrentCustomerId
{
    /**
     * @param GetThemeByCurrentCustomerId $getThemeByCurrentCustomerId
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     * @param Visibility $productVisibility
     * @param Config $catalogConfig
     * @param SalableProcessor $salableProcessor
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        protected GetThemeByCurrentCustomerId $getThemeByCurrentCustomerId,
        protected CollectionFactory $collectionFactory,
        protected StoreManagerInterface $storeManager,
        protected Visibility $productVisibility,
        protected Config $catalogConfig,
        protected SalableProcessor $salableProcessor,
        protected ConfigHelper $configHelper,
    ) {
    }

    /**
     * @param int $themeId
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute($themeId)
    {
        $themeCustomer = $this->getThemeByCurrentCustomerId->execute($themeId);
        $categoryIds = $themeCustomer->getCategoryIds();
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        $productCollection = $this->collectionFactory->create();
        $productCollection
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addAttributeToSelect($this->configHelper->getProductSizeAttributeCode())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->setVisibility($this->productVisibility->getVisibleInCatalogIds());
        $productCollection->addCategoriesFilter(['in' => $categoryIds]);
        return $this->salableProcessor->process($productCollection);
    }
}
