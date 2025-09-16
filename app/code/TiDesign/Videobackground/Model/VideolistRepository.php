<?php
namespace TiDesign\Videobackground\Model;

use TiDesign\Videobackground\Api\Data;
use TiDesign\Videobackground\Api\VideolistRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use TiDesign\Videobackground\Model\ResourceModel\Videolist as ResourceVideolist;
use TiDesign\Videobackground\Model\ResourceModel\Videolist\CollectionFactory as VideolistCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class VideolistRepository implements VideolistRepositoryInterface
{
    protected $resource;

    protected $videolistFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataVideolistFactory;

    private $storeManager;
	
	protected $_collectionFactory;

    public function __construct(
        ResourceVideolist $resource,
        VideolistFactory $videolistFactory,
        Data\VideolistInterfaceFactory $dataVideolistFactory,
        DataObjectHelper $dataObjectHelper,
		DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
		VideolistCollectionFactory $collectionFactory
    ) {
        $this->resource = $resource;
		$this->videolistFactory 		= $videolistFactory;
        $this->dataObjectHelper 		= $dataObjectHelper;
        $this->dataVideolistFactory 	= $dataVideolistFactory;
		$this->dataObjectProcessor 		= $dataObjectProcessor;
        $this->storeManager 			= $storeManager;
		$this->_collectionFactory 		= $collectionFactory;
    }

    public function save(\TiDesign\Videobackground\Api\Data\VideolistInterface $videolist)
    {
        try {
            $this->resource->save($videolist);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save : %1', $exception->getMessage()),
                $exception
            );
        }
        return $videolist;
    }

    public function getById($videolistId)
    {
		$videolist = $this->videolistFactory->create();
        $videolist->load($videolistId);
        if (!$videolist->getId()) {
            throw new NoSuchEntityException(__('Video list with id "%1" does not exist.', $videolistId));
        }
        return $videolist;
    }
	
    public function delete(\TiDesign\Videobackground\Api\Data\VideolistInterface $videolist)
    {
        try {
            $this->resource->delete($videolist);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete : %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    public function deleteById($videolistId)
    {
        return $this->delete($this->getById($videolistId));
    }
	
    public function getList()
    {
        $collection = $this->_collectionFactory->create();
        $collection->load();

        return $collection;		
    }
}
?>
