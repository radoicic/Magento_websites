<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price;

/**
 * Source item price options processor
 */
class Processor extends \Ecombricks\InventoryCommon\Model\SourceItem\Option\Processor
{
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterfaceFactory $optionFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Save $saveOptions
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Delete $deleteOptions
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Meta $optionMeta
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterfaceFactory $optionFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Save $saveOptions,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Delete $deleteOptions,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Meta $optionMeta,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
    )
    {
        parent::__construct($optionFactory, $saveOptions, $deleteOptions, $optionMeta, $dataObjectHelper);
    }
}