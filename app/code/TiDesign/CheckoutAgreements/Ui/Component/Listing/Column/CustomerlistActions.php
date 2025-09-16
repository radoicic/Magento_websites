<?php

namespace TiDesign\CheckoutAgreements\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class CustomerlistActions extends Column
{
	private $_urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->uiComponentFactory = $uiComponentFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
		$this->_urlBuilder = $urlBuilder;
    }

    public function prepareDataSource(array $dataSource)
    {
		$content = '';

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
				if($item['contract_id'] != null){
					$item[$this->getData('name')] = "<a data-src='#hidden-form' class='td-grid-button button-deny delete-customer' data-customer='".$item['customer_id']."' data-log='".$item['log_id']."'>Delete Record</a>";
				}else{
					$item[$this->getData('name')] ="";
				}
            }
        }
        return $dataSource;
    }
}
