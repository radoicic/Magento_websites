<?php

namespace TiDesign\CheckoutAgreements\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class TdMessage extends Column
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
				$item[$this->getData('name')] = $item['message'];
				if($item['filename']){
					$mediaPath = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
					$filePath 	= $mediaPath . 'TiDesign/contracts/'.$item['filename'];
					$item[$this->getData('name')] .= "<br><a class='pdf-file'  target='_blank' type='application/pdf' href='".$filePath."'><i class='fa fa-file-pdf-o' aria-hidden='true'></i> ".$item['filename']."</a>";
				}
            }
        }
        return $dataSource;
    }
}
