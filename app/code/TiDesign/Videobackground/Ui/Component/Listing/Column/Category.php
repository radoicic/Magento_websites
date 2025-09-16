<?php

namespace TiDesign\Videobackground\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Category extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $categoryIds = array_filter(explode(',',$item['category_id']));
                $categories = array();
                
                if (count($categoryIds)) {
                    foreach ($categoryIds as $categoryId) {
                        $category = $objectManager->create('Magento\Catalog\Model\Category')->load($categoryId);
                        $categories[] = $category->getName();
                    }
                }
                $item[$fieldName] = implode(', ', $categories);
            }
        }

        return $dataSource;
    }
}