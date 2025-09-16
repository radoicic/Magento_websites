<?php

namespace TiDesign\Domainmanager\Model\Config;

use Magento\Framework\Option\ArrayInterface;
use Exception;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Psr\Log\LoggerInterface;

class Sourcelist implements ArrayInterface
{
    /**
     * @var SourceRepositoryInterface
     */
    private $sourceRepository;
 
    /**
     * @var LoggerInterface
     */
    private $logger;
	
	public function __construct(
        SourceRepositoryInterface $sourceRepository,
        LoggerInterface $logger
	){
        $this->sourceRepository = $sourceRepository;
        $this->logger = $logger;
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