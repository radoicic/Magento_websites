<?php
namespace TiDesign\CheckoutAgreements\Model;

use TiDesign\CheckoutAgreements\Api\Data;
use TiDesign\CheckoutAgreements\Api\LoglistRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use TiDesign\CheckoutAgreements\Model\ResourceModel\Loglist as ResourceLoglist;
use TiDesign\CheckoutAgreements\Model\ResourceModel\Loglist\CollectionFactory as LoglistCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class LoglistRepository implements LoglistRepositoryInterface
{
    protected $resource;

    protected $loglistFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataLoglistFactory;

    private $storeManager;
	
	protected $_collectionFactory;

    public function __construct(
        ResourceLoglist $resource,
        LoglistFactory $loglistFactory,
        Data\LoglistInterfaceFactory $dataLoglistFactory,
        DataObjectHelper $dataObjectHelper,
		DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
		LoglistCollectionFactory $collectionFactory
    ) {
        $this->resource = $resource;
		$this->loglistFactory 			= $loglistFactory;
        $this->dataObjectHelper 		= $dataObjectHelper;
        $this->dataLoglistFactory 		= $dataLoglistFactory;
		$this->dataObjectProcessor 		= $dataObjectProcessor;
        $this->storeManager 			= $storeManager;
		$this->_collectionFactory 		= $collectionFactory;
    }

    public function save(\TiDesign\CheckoutAgreements\Api\Data\LoglistInterface $loglist)
    {
        try {
            $this->resource->save($loglist);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save : %1', $exception->getMessage()),
                $exception
            );
        }
        return $loglist;
    }

    public function getById($loglistId)
    {
		$loglist = $this->loglistFactory->create();
        $loglist->load($loglistId);
        if (!$loglist->getId()) {
            throw new NoSuchEntityException(__('Data with id "%1" does not exist.', $loglistId));
        }
        return $loglist;
    }
	
    public function delete(\TiDesign\CheckoutAgreements\Api\Data\LoglistInterface $loglist)
    {
        try {
            $this->resource->delete($loglist);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete : %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    public function deleteById($loglistId)
    {
        return $this->delete($this->getById($loglistId));
    }
	
    public function getList()
    {
        $collection = $this->_collectionFactory->create();
        $collection->load();

        return $collection;		
    }
}
?>
