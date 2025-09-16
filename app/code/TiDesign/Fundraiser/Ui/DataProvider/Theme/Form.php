<?php

namespace TiDesign\Fundraiser\Ui\DataProvider\Theme;

use Magento\Ui\DataProvider\AbstractDataProvider;
use TiDesign\Fundraiser\Model\ResourceModel\Theme\CollectionFactory;

class Form extends AbstractDataProvider
{
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        protected CollectionFactory $collectionFactory,
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
        $result = [$item[$this->primaryFieldName] => $item];
        return $result;
    }
}
