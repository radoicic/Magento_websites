<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass;

/**
 * Get source item tax class options
 */
class Get extends \Ecombricks\InventoryCommon\Model\SourceItem\Option\Get 
    implements \Ecombricks\InventoryCustomerSourceSelection\Api\Inventory\SourceItem\Option\TaxClass\GetInterface 
{
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\TaxClass\Get $resource
     * @param \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterfaceFactory $optionFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Meta $optionMeta
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\TaxClass\Get $resource,
        \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterfaceFactory $optionFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Meta $optionMeta,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
    )
    {
        parent::__construct($resource, $optionFactory, $optionMeta, $dataObjectHelper);
    }
}