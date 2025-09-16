<?php

namespace TiDesign\Consignment\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use \Magento\Store\Api\WebsiteRepositoryInterface;

class TdWebsite extends Column
{
	protected 	$websiteRepository;

    public function __construct(
		ContextInterface $context,
		UiComponentFactory $uiComponentFactory,
		WebsiteRepositoryInterface $websiteRepository,
        array $components = [],
        array $data = []
    ) {
        $this->websiteRepository = $websiteRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    public function prepareDataSource(array $dataSource)
    {
		$content = '';

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {

				$websiteId = $item['td_website'];
				$website= $this->websiteRepository->getById($websiteId);
				$item[$this->getData('name')] = $website->getName();
            }
        }
        return $dataSource;
    }
}
