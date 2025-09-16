<?php
namespace TiDesign\Consignment\Model\Filters\Website;

use Magento\Framework\Option\ArrayInterface;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory;

class WebsiteList implements ArrayInterface
{

    public function __construct(
        CollectionFactory $websiteRepository
    ) {
        $this->websiteRepository = $websiteRepository;
    }
    
	public function toOptionArray($addEmpty = true)
	{
		$options = [];
		try {
			$websiteData = $this->websiteRepository->create();
			foreach ($websiteData as $source) {
				$options[] = ['label' => $source->getName(), 'value' => $source->getWebsiteId()];
			}
			
		}catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
		return $options;
	}
}