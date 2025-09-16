<?php
namespace TiDesign\Consignment\Model\Filters\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;

class SourceList implements ArrayInterface
{
    protected $_categoryCollectionFactory;

    public function __construct(
        SourceRepositoryInterface $sourceRepository
    ) {
        $this->sourceRepository = $sourceRepository;
    }
    
	public function toOptionArray($addEmpty = true)
	{
		$options = [];
		try {
			$sourceData = $this->sourceRepository->getList()->getItems();
			foreach ($sourceData as $source) {
				$options[] = ['label' => $source->getName(), 'value' => $source->getSourceCode()];
			}
			
		}catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
		return $options;
	}
}

