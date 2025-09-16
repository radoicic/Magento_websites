<?php

namespace TiDesign\Videobackground\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Video extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
				if($item['video_source'] == 0){
					$item[$fieldName] = "<span>Youtube Video</span><br><a href='".$item['video_url']."' target='_blank'>".$item['video_url']." <span class='fa fa-fw fa-external-link'></span></a>";
				}else{
					$item[$fieldName] = "Local Video";
				}
            }
        }

        return $dataSource;
    }	
}