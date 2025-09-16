<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\ResourceModel\Order;

/**
 * Order resource shipping method wrapper
 */
class ShippingMethod extends \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Framework\ResourceModel\SourceOption
{
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory
     * @param string $sourceOptionTable
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory,
        string $sourceOptionTable = 'ecombricks_inventory__sales_order_source_shipping_method'
    )
    {
        parent::__construct(
            $objectReflectionFactory,
            $sourceOptionTable
        );
    }
}