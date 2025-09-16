<?php
namespace TiDesign\Videobackground\Model\Videolist;

use TiDesign\Videobackground\Model\ResourceModel\Videolist\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\StoreManagerInterface;
/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Webtorn\Samplefiles\Model\ResourceModel\Samplefiles\Collection
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
        /** @var $news \Webtorn\Samplefiles\Model\Samplefiles */
        foreach ($items as $news) {
            $this->loadedData[$news->getId()] = $news->getData();
        }
		
		foreach ($items as $item) {
			$data = $item->getData();
			
			
			$data['category_id'] 	= array_filter(explode(',', $data['category_id']));
			$data['store_id'] 		= array_filter(explode(',', $data['store_id']));
			$data['static_pages'] 	= array_filter(explode(',', $data['static_pages']));
			$data['page_id'] 		= array_filter(explode(',', $data['page_id']));
			
			
			if($data['local_webm']){
				$xname = $data['local_webm'];
				$s['local_webm'][0]['name'] = $xname;		
				$s['local_webm'][0]['url'] = $this->getMediaUrl().$xname;		
			}else{
				$s['local_webm'] = '';
			}
			
			if($data['local_mp4']){
				$xname2 = $data['local_mp4'];
				$s['local_mp4'][0]['name'] = $xname2;		
				$s['local_mp4'][0]['url'] = $this->getMediaUrl().$xname2;		
			}else{
				$s['local_mp4'] = '';
			}			
			
			
			$this->loadedData[$item->getId()]['category_id'] 	= $data['category_id'];
			$this->loadedData[$item->getId()]['store_id'] 		= $data['store_id'];
			$this->loadedData[$item->getId()]['page_id'] 		= $data['page_id'];
			$this->loadedData[$item->getId()]['static_pages'] 	= $data['static_pages'];
			
			$this->loadedData[$item->getId()]['local_webm'] 	= $s['local_webm'];
			$this->loadedData[$item->getId()]['local_mp4'] 		= $s['local_mp4'];
			
			$fullData = $this->loadedData;
			//$this->loadedData[$item->getId()] = array_merge($fullData[$item->getId()], $m);
		}

        $data = $this->dataPersistor->get('samplefiles_samplefiles');
        if (!empty($data)) {
            $news = $this->collection->getNewEmptyItem();
            $news->setData($data);
            $this->loadedData[$news->getId()] = $news->getData();
            $this->dataPersistor->clear('samplefiles_samplefiles');
        }

        return $this->loadedData;
    }
	public function getMediaUrl()
    {
        $mediaUrl = $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'TiDesign/videobackground/';
        return $mediaUrl;
    }
}
