<?php

namespace TiDesign\Consignment\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Framework\App\ResourceConnection;

class TdSource extends Column
{
	private 	$sourceRepository;
	protected 	$_resource;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ResourceConnection $resource
     * @param SourceRepositoryInterface $sourceRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
		ContextInterface $context,
		UiComponentFactory $uiComponentFactory,
		ResourceConnection $resource,
		SourceRepositoryInterface $sourceRepository,
        array $components = [],
        array $data = []
    ) {
		$this->_resource 		= $resource;
        $this->sourceRepository = $sourceRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    public function prepareDataSource(array $dataSource)
    {
		$content = '';

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
				$sourceCode = $item['td_source'];

				$connection = $this->_resource->getConnection();
				$tableName 	= $connection->getTableName('inventory_source');
				$catsql 	= "SELECT * FROM " . $tableName . " WHERE source_code ='" .$sourceCode."'";
				$sc 		= $connection->fetchAll($catsql);
				if(count($sc)){

					$sourceInfo = $this->sourceRepository->get($sourceCode);
					if($sourceInfo){
						$sourceName = $sourceInfo->getName();
						$sourceName .= (($sourceInfo->isEnabled()==true) ? "<br> <span class='txt-green'>(Enabled)</span>" : "<br> <span class='txt-red'>(Disabled)</span>");
						$item[$this->getData('name')] = $sourceName;
					}

				}else{
					$sourceName = "<span class='txt-red'>!!! SOURCE NOT FOUND</span>";
					$item[$this->getData('name')] = $sourceName;
				}
            }
        }
        return $dataSource;
    }
}
