<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventorySourceSelection\Algorithms;

/**
 * Priority based source selection algorithm plugin
 */
class PriorityBasedAlgorithm extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Get default sorted sources result
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySourceSelection\Algorithms\Result\GetDefaultSortedSourcesResult 
     */
    private $getDefaultSortedSourcesResult;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySourceSelection\Algorithms\Result\GetDefaultSortedSourcesResult $getDefaultSortedSourcesResult
     * @retun void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySourceSelection\Algorithms\Result\GetDefaultSortedSourcesResult $getDefaultSortedSourcesResult
    )
    {
        parent::__construct($wrapperFactory);
        $this->getDefaultSortedSourcesResult = $getDefaultSortedSourcesResult;
    }
    
    /**
     * Around execute
     * 
     * @param \Magento\InventorySourceSelection\Model\Algorithms\PriorityBasedAlgorithm
     * @param \Closure $proceed
     * @return \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterface
     */
    public function aroundExecute(
        \Magento\InventorySourceSelection\Model\Algorithms\PriorityBasedAlgorithm $subject,
        \Closure $proceed,
        \Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterface $inventoryRequest
    ) : \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterface
    {
        $this->setSubject($subject);
        $sortedSources = $this->invokeSubjectMethod('getEnabledSourcesOrderedByPriorityByStockId', $inventoryRequest->getStockId());
        return $this->getDefaultSortedSourcesResult->execute($inventoryRequest, $sortedSources);
    }
}