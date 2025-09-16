<?php

namespace TiDesign\Consignment\Model\Config;

use Magento\Framework\Option\ArrayInterface;
use Exception;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Psr\Log\LoggerInterface;

class Listcount implements ArrayInterface
{
 
    /**
     * @var LoggerInterface
     */
    private $logger;
	
	public function __construct(
        LoggerInterface $logger
	){
        $this->logger = $logger;
	}
	public function toOptionArray($addEmpty = true)
	{
		$objectManager 	= \Magento\Framework\App\ObjectManager::getInstance();
		$helper 		= $objectManager->get('TiDesign\Consignment\Helper\Consignment');		
		$options = [];
		try {
			$pagerLimits = explode(",", $helper->getConfig("catalog/frontend/list_per_page_values"));
			$showAll = $helper->getConfig("catalog/frontend/list_allow_all");
			
			foreach ($pagerLimits as $_limit) {
				$options[] = ['label' => $_limit, 'value' => $_limit];
			}
			if($showAll){
				$options[] = ['label' => "All", 'value' => "all"];
			}
			
		}catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
		return $options;
	}
}