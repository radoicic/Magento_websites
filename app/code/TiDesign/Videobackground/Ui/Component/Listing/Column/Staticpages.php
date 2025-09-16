<?php

namespace TiDesign\Videobackground\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Staticpages extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
				$item[$fieldName] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }
    protected function prepareItem(array $item)
    {
	
        $content = '';	
		$pageIds = array_filter(explode(',',$item['static_pages']));
		if (count($pageIds)) {
			foreach ($pageIds as $pageId) {
				$pages = [
					1 => __('Home page'), 
					2 => __('Checkout'),
					3 => __('Cart'),
					4 => __('Contact')
				];
				$content .= $pages[$pageId] . "<br/>";
			}
		}		
        return $content;
    }	


	
	
}