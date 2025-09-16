<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass;

/**
 * Source item tax class options processor
 */
class Processor extends \Ecombricks\InventoryCommon\Model\SourceItem\Option\Processor
{
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterfaceFactory $optionFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Save $saveOptions
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Delete $deleteOptions
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Meta $optionMeta
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterfaceFactory $optionFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Save $saveOptions,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Delete $deleteOptions,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Meta $optionMeta,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
    )
    {
        parent::__construct($optionFactory, $saveOptions, $deleteOptions, $optionMeta, $dataObjectHelper);
    }
}