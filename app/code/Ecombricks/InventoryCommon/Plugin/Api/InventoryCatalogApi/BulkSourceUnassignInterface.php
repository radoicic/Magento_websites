<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Plugin\Api\InventoryCatalogApi;

/**
 * Bulk source unassign interface
 */
class BulkSourceUnassignInterface
{
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
     * @param \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Unassign $unassignSourceItemOptions
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config $optionConfig
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Unassign $unassignSourceItemOptions,
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config $optionConfig
    )
    {
        $this->unassignSourceItemOptions = $unassignSourceItemOptions;
        $this->optionConfig = $optionConfig;
    }

    /**
     * After execute
     *
     * @param \Magento\InventoryCatalogApi\Api\BulkSourceUnassignInterface $subject
     * @param int $result
     * @param array $skus
     * @param array $sources
     * @return int
     */
    public function afterExecute(
        \Magento\InventoryCatalogApi\Api\BulkSourceUnassignInterface $subject,
        int $result,
        array $skus,
        array $sources
    ): int
    {
        if ($this->optionConfig->isEnabled()) {
            $this->unassignSourceItemOptions->execute($skus, $sources);
        }
        return $result;
    }
}