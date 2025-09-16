<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventoryDistanceBasedSourceSelection\Algorithms;

/**
 * Distance based source selection algorithm plugin
 */
class DistanceBasedAlgorithm extends \Ecombricks\Common\Plugin\Plugin 
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
     * @param \Magento\InventoryDistanceBasedSourceSelection\Model\Algorithms\DistanceBasedAlgorithm $subject
     * @param \Closure $proceed
     * @param \Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterface $inventoryRequest
     * @return \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterface
     */
    public function aroundExecute(
        $subject,
        \Closure $proceed,
        \Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterface $inventoryRequest
    ) : \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterface
    {
        $this->setSubject($subject);
        $destinationAddress = $inventoryRequest->getExtensionAttributes()->getDestinationAddress();
        if ($destinationAddress === null) {
            throw new \Magento\Framework\Exception\LocalizedException(__('No destination address was provided in the request'));
        }
        $sortedSources = $this->invokeSubjectMethod('getEnabledSourcesOrderedByDistanceByStockId', $inventoryRequest->getStockId(), $destinationAddress);
        return $this->getDefaultSortedSourcesResult->execute($inventoryRequest, $sortedSources);
    }
}