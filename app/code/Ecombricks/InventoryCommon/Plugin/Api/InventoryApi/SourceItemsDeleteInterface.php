<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Plugin\Api\InventoryApi;

/**
 * Source items delete interface
 */
class SourceItemsDeleteInterface
{
    /**
     * Delete source item options
     * 
     * @var \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Delete
     */
    private $deleteOptions;

    /**
     * Source item option configuration
     * 
     * @var \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config
     */
    private $optionConfig;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Delete $deleteOptions
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config $optionConfig
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Delete $deleteOptions,
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config $optionConfig
    )
    {
        $this->deleteOptions = $deleteOptions;
        $this->optionConfig = $optionConfig;
    }

    /**
     * After execute
     *
     * @param \Magento\InventoryApi\Api\SourceItemsDeleteInterface $subject
     * @param void $result
     * @param array $sourceItems
     * @return void
     */
    public function afterExecute(
        \Magento\InventoryApi\Api\SourceItemsDeleteInterface $subject,
        $result,
        array $sourceItems
    )
    {
        if ($this->optionConfig->isEnabled()) {
            $this->deleteOptions->execute($sourceItems);
        }
    }
}