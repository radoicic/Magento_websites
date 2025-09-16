<?php

namespace TiDesign\Domainmanager\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class TdStatus extends Column
{
    public function prepareDataSource(array $dataSource)
    {
		$content = '';

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {							
				if($item['td_status']==0){
					$item[$this->getData('name')] = "<span class='passiveurl'>". __("Passive")."</span>";
				}
				if($item['td_status']==1){
					$item[$this->getData('name')] = "<span class='activeurl'>". __("Active")."</span>";
				}
            }
        }
        return $dataSource;
    }
}
