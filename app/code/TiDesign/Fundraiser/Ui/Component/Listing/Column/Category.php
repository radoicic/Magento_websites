<?php

namespace TiDesign\Fundraiser\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use TiDesign\Fundraiser\Model\Catalog\Category\CategoryIdsToArrayProcessor;
use TiDesign\Fundraiser\Model\Catalog\Category\OptionSource\Categories;

class Category extends Column
{
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Categories $categories
     * @param CategoryIdsToArrayProcessor $categoryIdsToArrayProcessor
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        protected Categories $categories,
        protected CategoryIdsToArrayProcessor $categoryIdsToArrayProcessor,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareDataSource(array $dataSource)
    {
        $options = $this->categories->toOptionArray();
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $originalValue = $item[$this->getData('name')];
                $categoryIds = $this->categoryIdsToArrayProcessor->execute($originalValue);
                $categoryNames = array_map(fn($catId) => $options[$catId]['label'] ?? '', $categoryIds);
                $categoryNames = array_filter($categoryNames);
                $item[$this->getData('name')] = implode(" | ", $categoryNames);
            }
        }
        return $dataSource;
    }

}
