<?php

namespace TiDesign\Videobackground\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Videomute extends Column
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
		if($item['video_mute']==0){
			$content = __("No");
		}
		if($item['video_mute']==1){
			$content = __("Yes");
		}
		
        return $content;
    }	
}