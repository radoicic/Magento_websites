<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass;

/**
 * Save source item tax class options
 */
class Save extends \Ecombricks\InventoryCommon\Model\SourceItem\Option\Save 
    implements \Ecombricks\InventoryCustomerSourceSelection\Api\Inventory\SourceItem\Option\TaxClass\SaveInterface 
{
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\TaxClass\Save $resource
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Meta $optionMeta
     * @param \Psr\Log\LoggerInterface $logger
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\ResourceModel\SourceItem\Option\TaxClass\Save $resource,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Meta $optionMeta,
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