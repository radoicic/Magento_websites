<?php

namespace TiDesign\Videobackground\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Cms\Model\Page;

class Cms extends Column
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
	
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$cmsIds = explode(',',$item['page_id']);
		//$cmslist = array();

		if (count($cmsIds)) {
			foreach ($cmsIds as $cmsId) {
				$cms = $objectManager->create('Magento\Cms\Model\Page')->load($cmsId);
				//$cmslist[] = $cms->getTitle();
				
				$content .= $cms->getTitle() . "<br/>";
			}
		}	
	
	
	
        return $content;
    }	


	
	
}