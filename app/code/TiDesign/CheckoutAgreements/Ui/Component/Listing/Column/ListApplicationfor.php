<?php

namespace TiDesign\CheckoutAgreements\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class ListApplicationfor extends Column
{
	private $_urlBuilder;
	private $_timezone;
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
		TimezoneInterface $timezone,
		UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->uiComponentFactory = $uiComponentFactory;
		$this->_timezone = $timezone;
        parent::__construct($context, $uiComponentFactory, $components, $data);
		$this->_urlBuilder = $urlBuilder;
    }

    public function prepareDataSource(array $dataSource)
    {
		$content = '';

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
				$content = "";

				if($item['filename']){
/*
					$application_for = json_decode($item['form_data'], true)['application_for'];
*/
					$application_for = json_decode($item['form_data'], true)['application_for'] ?? null;
					if($application_for){
						$content .= $application_for;
					}else{
						$content .= "<p class='red'>NOT FILLED!!!</p>";
					}
				}else{
					$content	.= "";
				}

				$item[$this->getData('name')] =$content;
            }
        }
        return $dataSource;
    }
}
