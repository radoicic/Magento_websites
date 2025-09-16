<?php
namespace TiDesign\Domainmanager\Model;

use TiDesign\Domainmanager\Api\Data;
use TiDesign\Domainmanager\Api\DomainlistRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use TiDesign\Domainmanager\Model\ResourceModel\Domainlist as ResourceDomainlist;
use TiDesign\Domainmanager\Model\ResourceModel\Domainlist\CollectionFactory as DomainlistCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class DomainlistRepository implements DomainlistRepositoryInterface
{
    protected $resource;

    protected $domainlistFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataDomainlistFactory;

    private $storeManager;
	
	protected $_collectionFactory;

    public function __construct(
        ResourceDomainlist $resource,
        DomainlistFactory $domainlistFactory,
        Data\DomainlistInterfaceFactory $dataDomainlistFactory,
        DataObjectHelper $dataObjectHelper,
		DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
		DomainlistCollectionFactory $collectionFactory
    ) {
        $this->resource = $resource;
		$this->domainlistFactory 		= $domainlistFactory;
        $this->dataObjectHelper 		= $dataObjectHelper;
        $this->dataDomainlistFactory 	= $dataDomainlistFactory;
		$this->dataObjectProcessor 		= $dataObjectProcessor;
        $this->storeManager 			= $storeManager;
		$this->_collectionFactory 		= $collectionFactory;
    }

    public function save(\TiDesign\Domainmanager\Api\Data\DomainlistInterface $domainlist)
    {
        try {
            $this->resource->save($domainlist);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save : %1', $exception->getMessage()),
                $exception
            );
        }
        return $domainlist;
    }

    public function getById($domainlistId)
    {
		$domainlist = $this->domainlistFactory->create();
        $domainlist->load($domainlistId);
        if (!$domainlist->getId()) {
            throw new NoSuchEntityException(__('Domain list with id "%1" does not exist.', $domainlistId));
        }
        return $domainlist;
    }
	
    public function delete(\TiDesign\Domainmanager\Api\Data\DomainlistInterface $domainlist)
    {
        try {
            $this->resource->delete($domainlist);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete : %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    public function deleteById($domainlistId)
    {
        return $this->delete($this->getById($domainlistId));
    }
	
    public function getList()
    {
        $collection = $this->_collectionFactory->create();
        $collection->load();

        return $collection;		
    }
}
?>
