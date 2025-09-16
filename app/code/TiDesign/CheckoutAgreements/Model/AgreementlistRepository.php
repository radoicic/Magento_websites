<?php
/**
 * Copyright © TiDesign All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace TiDesign\CheckoutAgreements\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use TiDesign\CheckoutAgreements\Api\AgreementlistRepositoryInterface;
use TiDesign\CheckoutAgreements\Api\Data\AgreementlistInterface;
use TiDesign\CheckoutAgreements\Api\Data\AgreementlistInterfaceFactory;
use TiDesign\CheckoutAgreements\Api\Data\AgreementlistSearchResultsInterfaceFactory;
use TiDesign\CheckoutAgreements\Model\ResourceModel\Agreementlist as ResourceAgreementlist;
use TiDesign\CheckoutAgreements\Model\ResourceModel\Agreementlist\CollectionFactory as AgreementlistCollectionFactory;

class AgreementlistRepository implements AgreementlistRepositoryInterface
{

    /**
     * @var Agreementlist
     */
    protected $searchResultsFactory;

    /**
     * @var AgreementlistInterfaceFactory
     */
    protected $agreementlistFactory;

    /**
     * @var ResourceAgreementlist
     */
    protected $resource;

    /**
     * @var AgreementlistCollectionFactory
     */
    protected $agreementlistCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;


    /**
     * @param ResourceAgreementlist $resource
     * @param AgreementlistInterfaceFactory $agreementlistFactory
     * @param AgreementlistCollectionFactory $agreementlistCollectionFactory
     * @param AgreementlistSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceAgreementlist $resource,
        AgreementlistInterfaceFactory $agreementlistFactory,
        AgreementlistCollectionFactory $agreementlistCollectionFactory,
        AgreementlistSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->agreementlistFactory = $agreementlistFactory;
        $this->agreementlistCollectionFactory = $agreementlistCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(AgreementlistInterface $agreementlist)
    {
        try {
            $this->resource->save($agreementlist);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the agreementlist: %1',
                $exception->getMessage()
            ));
        }
        return $agreementlist;
    }

    /**
     * @inheritDoc
     */
    public function get($agreementlistId)
    {
        $agreementlist = $this->agreementlistFactory->create();
        $this->resource->load($agreementlist, $agreementlistId);
        if (!$agreementlist->getId()) {
            throw new NoSuchEntityException(__('Agreementlist with id "%1" does not exist.', $agreementlistId));
        }
        return $agreementlist;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->agreementlistCollectionFactory->create();
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(AgreementlistInterface $agreementlist)
    {
        try {
            $agreementlistModel = $this->agreementlistFactory->create();
            $this->resource->load($agreementlistModel, $agreementlist->getAgreementlistId());
            $this->resource->delete($agreementlistModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Agreementlist: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($agreementlistId)
    {
        return $this->delete($this->get($agreementlistId));
    }
}

