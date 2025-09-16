<?php

namespace TiDesign\Consignment\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Catalog\Model\CategoryFactory;

class TdCategory extends Column
{
	protected $_categoryFactory;

    public function __construct(
		ContextInterface $context,
		UiComponentFactory $uiComponentFactory,
		CategoryFactory $categoryFactory,
        array $components = [],
        array $data = []
    ) {
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    public function prepareDataSource(array $dataSource)
    {
		$content = '';

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {

				$categoryId = $item['td_category'];
				$category = $this->_categoryFactory->create()->load($categoryId);
				if($category->getName()){
					$categoryName = $category->getName();
					$categoryId = $category->getId();
					$item[$this->getData('name')] = "<strong>".$categoryName."</strong>  <br>(ID: ".$categoryId.") (".$category->getProductCount().")";
				}else{
					$item[$this->getData('name')] ="<span class='txt-red'>!!! CATEGORY NOT FOUND</span>";
				}
            }
        }
        return $dataSource;
    }
}
