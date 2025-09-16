<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\ResourceModel\Quote\Item\Collection;

/**
 * Quote item collection source wrapper
 */
class Source extends \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Framework\ResourceModel\Collection\Source
{
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory
     * @param string $sourceTable
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory,
        string $sourceTable = 'ecombricks_inventory__quote_item_source'
    )
    {
        parent::__construct(
            $objectReflectionFactory,
            $sourceTable
        );
    }
}