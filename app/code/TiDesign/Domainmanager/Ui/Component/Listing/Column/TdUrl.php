<?php

namespace TiDesign\Domainmanager\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class TdUrl extends Column
{
	
    public function prepareDataSource(array $dataSource)
    {
		

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {		
                $urlCol = array_filter(explode(',',$item['td_url']));
				$content = "";
				if (count($urlCol)) {
					foreach ($urlCol as $urlAd) {
						$content .= "<a href='https://".$urlAd."' target='_blank'>".$urlAd." <span class='fa fa-fw fa-external-link'></span></a>";
					}
				}
				$item['td_url'] = str_replace("</a><a","</a><br/><a", $content);			
            }
        }
		
        return $dataSource;
    }
}