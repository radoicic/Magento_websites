<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model\SourceItem\Option;

/**
 * Delete source item options
 */
class Delete implements \Ecombricks\InventoryCommon\Api\SourceItem\Option\DeleteInterface
{
    /**
     * Resource
     * 
     * @var \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Delete
     */
    private $resource;

    /**
     * Source item option meta
     * 
     * @var \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta
     */
    private $optionMeta;

    /**
     * Logger
     * 
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Delete $resource
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta $optionMeta
     * @param \Psr\Log\LoggerInterface $logger
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Delete $resource,
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta $optionMeta,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->resource = $resource;
        $this->optionMeta = $optionMeta;
        $this->logger = $logger;
    }
    
    /**
     * Execute
     * 
     * @param \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface[] $options
     * @return $this
     */
    public function execute(array $options)
    {
        try {
            $this->resource->execute($options);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__('Could not delete %1.', $this->optionMeta->getLabel()), $exception);
        }
        return $this;
    }
}