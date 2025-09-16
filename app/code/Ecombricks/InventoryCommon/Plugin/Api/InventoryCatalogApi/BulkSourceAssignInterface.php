<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Plugin\Api\InventoryCatalogApi;

/**
 * Bulk source assign interface
 */
class BulkSourceAssignInterface
{
    /**
     * Assign source item options
     * 
     * @var \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Assign
     */
    private $assignSourceItemOptions;

    /**
     * Source item option configuration
     * 
     * @var \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config
     */
    private $optionConfig;

    /**
     * Construct
     * 
     * @param \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Assign $assignSourceItemOptions
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config $optionConfig
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Assign $assignSourceItemOptions,
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config $optionConfig
    )
    {
        $this->assignSourceItemOptions = $assignSourceItemOptions;
        $this->optionConfig = $optionConfig;
    }

    /**
     * After execute
     *
     * @param \Magento\InventoryCatalogApi\Api\BulkSourceAssignInterface $subject
     * @param int $result
     * @param array $skus
     * @param array $sources
     * @return int
     */
    public function afterExecute(
        \Magento\InventoryCatalogApi\Api\BulkSourceAssignInterface $subject,
        int $result,
        array $skus,
        array $sources
    ): int
    {
        if ($this->optionConfig->isEnabled()) {
            $this->assignSourceItemOptions->execute($skus, $sources);
        }
        return $result;
    }
}