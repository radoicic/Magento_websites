<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price;

/**
 * Save source item price options
 */
class Save extends \Ecombricks\InventoryCommon\Model\SourceItem\Option\Save 
    implements \Ecombricks\InventoryCustomerSourceSelection\Api\Inventory\SourceItem\Option\Price\SaveInterface
{
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\Price\Save $resource
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Meta $optionMeta
     * @param \Psr\Log\LoggerInterface $logger
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\Price\Save $resource,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Meta $optionMeta,
        \Psr\Log\LoggerInterface $logger
    )
    {
        parent::__construct(
            $resource,
            $optionMeta,
            $logger
        );
    }
}