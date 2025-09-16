<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Plugin\Api\InventoryCatalogApi;

/**
 * Bulk inventory transfer interface plugin
 */
class BulkInventoryTransferInterface
{
    /**
     * Transfer source item options
     * 
     * @var \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Transfer
     */
    private $transferSourceItemOptions;

    /**
     * Unassign source item options
     * 
     * @var \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Unassign
     */
    private $unassignSourceItemOptions;

    /**
     * Source item option configuration
     * 
     * @var \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config
     */
    private $optionConfig;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Transfer $transferSourceItemOptions
     * @param \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Unassign $unassignSourceItemOptions
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config $optionConfig
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Transfer $transferSourceItemOptions,
        \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Unassign $unassignSourceItemOptions,
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config $optionConfig
    )
    {
        $this->transferSourceItemOptions = $transferSourceItemOptions;
        $this->unassignSourceItemOptions = $unassignSourceItemOptions;
        $this->optionConfig = $optionConfig;
    }

    /**
     * Around execute
     * 
     * @param \Magento\InventoryCatalogApi\Api\BulkInventoryTransferInterface $subject
     * @param callable $proceed
     * @param array $skus
     * @param string $originSource
     * @param string $destinationSource
     * @param bool $unassignFromOrigin
     * @return bool
     */
    public function aroundExecute(
        \Magento\InventoryCatalogApi\Api\BulkInventoryTransferInterface $subject,
        callable $proceed,
        array $skus,
        string $originSource,
        string $destinationSource,
        bool $unassignFromOrigin
    ): bool
    {
        if ($this->optionConfig->isEnabled()) {
            $this->transferSourceItemOptions->execute($skus, $originSource, $destinationSource);
            $result = $proceed($skus, $originSource, $destinationSource, $unassignFromOrigin);
            if ($unassignFromOrigin) {
                $this->unassignSourceItemOptions->execute($skus, [$originSource]);
            }
            return $result;
        } else {
            return $proceed($skus, $originSource, $destinationSource, $unassignFromOrigin);
        }
    }
}