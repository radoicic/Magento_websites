<?php
namespace TiDesign\Consignment\Model;

use TiDesign\Consignment\Api\Data;
use TiDesign\Consignment\Api\ConsignmentlistRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use TiDesign\Consignment\Model\ResourceModel\Consignmentlist as ResourceConsignmentlist;
use TiDesign\Consignment\Model\ResourceModel\Consignmentlist\CollectionFactory as ConsignmentlistCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class ConsignmentlistRepository implements ConsignmentlistRepositoryInterface
{
    protected $resource;

    protected $consignmentlistFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataConsignmentlistFactory;

    private $storeManager;
	
	protected $_collectionFactory;

    public function __construct(
        ResourceConsignmentlist $resource,
        ConsignmentlistFactory $consignmentlistFactory,
        Data\ConsignmentlistInterfaceFactory $dataConsignmentlistFactory,
        DataObjectHelper $dataObjectHelper,
		DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
		ConsignmentlistCollectionFactory $collectionFactory
    ) {
        $this->resource = $resource;
		$this->consignmentlistFactory 		= $consignmentlistFactory;
        $this->dataObjectHelper 		= $dataObjectHelper;
        $this->dataConsignmentlistFactory 	= $dataConsignmentlistFactory;
		$this->dataObjectProcessor 		= $dataObjectProcessor;
        $this->storeManager 			= $storeManager;
		$this->_collectionFactory 		= $collectionFactory;
    }

    public function save(\TiDesign\Consignment\Api\Data\ConsignmentlistInterface $consignmentlist)
    {
        try {
            $this->resource->save($consignmentlist);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save : %1', $exception->getMessage()),
                $exception
            );
        }
        return $consignmentlist;
    }

    public function getById($consignmentlistId)
    {
		$consignmentlist = $this->consignmentlistFactory->create();
        $consignmentlist->load($consignmentlistId);
        if (!$consignmentlist->getId()) {
            throw new NoSuchEntityException(__('Consignment with id "%1" does not exist.', $consignmentlistId));
        }
        return $consignmentlist;
    }
	
    public function delete(\TiDesign\Consignment\Api\Data\ConsignmentlistInterface $consignmentlist)
    {
        try {
            $this->resource->delete($consignmentlist);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete : %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    public function deleteById($consignmentlistId)
    {
        return $this->delete($this->getById($consignmentlistId));
    }
	
    public function getList()
    {
        $collection = $this->_collectionFactory->create();
        $collection->load();

        return $collection;		
    }
}
?>
