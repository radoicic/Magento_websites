<?php
namespace TiDesign\Consignment\Model\Consignmentlist;

use TiDesign\Consignment\Model\ResourceModel\Consignmentlist\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\StoreManagerInterface;
/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \TiDesign\Consignment\Model\ResourceModel\Domainlist\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;
	
	
    protected $storeManager;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $allnewsCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $allnewsCollectionFactory,
        DataPersistorInterface $dataPersistor,
		StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $allnewsCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var $news \TiDesign\Consignment\Model\Consignmentlist */
        foreach ($items as $news) {
            $this->loadedData[$news->getTdId()] = $news->getData();
        }
		
		foreach ($items as $item) {
			$data = $item->getData();
			/*$data['td_url'] 	= array_filter(explode(',', $data['td_url']));
			
			$this->loadedData[$item->getTdId()]['td_url'] 	= $data['td_url'];
			foreach ($data['td_url'] as $tdurls){
				$this->loadedData[$item->getTdId()]['dynamic_rows_container'][]['td_url'] =  $tdurls;
			}
			*/
			
			$fullData = $this->loadedData;
		}

        $data = $this->dataPersistor->get('consignment_consignmentlist');
        if (!empty($data)) {
            $news = $this->collection->getNewEmptyItem();
            $news->setData($data);
            $this->loadedData[$news->getTdId()] = $news->getData();
            $this->dataPersistor->clear('consignment_consignmentlist');
        }

        return $this->loadedData;
    }
}
