<?php

namespace TiDesign\Fundraiser\Ui\DataProvider\ThemeCustomer;

use Magento\Ui\DataProvider\AbstractDataProvider;
use TiDesign\Fundraiser\Model\Catalog\Category\CategoryIdsToArrayProcessor;
use TiDesign\Fundraiser\Model\ResourceModel\ThemeCustomer\Grid\CollectionFactory;

class Form extends AbstractDataProvider
{
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param CategoryIdsToArrayProcessor $categoryIdsToArrayProcessor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        protected CollectionFactory $collectionFactory,
        protected CategoryIdsToArrayProcessor $categoryIdsToArrayProcessor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        $data = parent::getData();
        $items = $data['items'];
        $item = reset($items);
        $item['category_id'] = $this->categoryIdsToArrayProcessor->execute($item['category_id'] ?? null, true);
        $result = [$item[$this->primaryFieldName] => $item];
        return $result;
    }
}
