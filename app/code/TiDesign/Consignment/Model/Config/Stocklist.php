<?php
namespace TiDesign\Consignment\Model\Config;

use Magento\Framework\Option\ArrayInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\Data\StockInterface;
use Magento\InventoryApi\Api\StockRepositoryInterface;

class Stocklist implements ArrayInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var StockRepositoryInterface
     */
    private $stockRepository;
 
    /**
     * @var LoggerInterface
     */
    private $logger;
	
	public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StockRepositoryInterface $stockRepository,
        LoggerInterface $logger
	){
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->stockRepository = $stockRepository;
        $this->logger = $logger;
	}
	public function toOptionArray($addEmpty = true)
	{
		$options = [];
		try {
			$searchCriteria = $this->searchCriteriaBuilder->create();
			$stockInfo = null;
			$stockData = $this->stockRepository->getList($searchCriteria);
			if ($stockData->getTotalCount()) {
				foreach ($stockData->getItems() as $stock) {
						$options[] = ['label' => $stock['name'], 'value' => $stock['stock_id']];
				}
            }
		}catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
		return $options;
	}
}