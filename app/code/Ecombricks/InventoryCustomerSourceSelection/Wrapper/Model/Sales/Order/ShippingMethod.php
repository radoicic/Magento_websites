<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order;

/**
 * Order shipping method wrapper
 */
class ShippingMethod extends \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Framework\SourceOption
{
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory
     * @param string $attributeCode
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory,
        string $attributeCode = 'shipping_method'
    )
    {
        parent::__construct(
            $objectReflectionFactory,
            $attributeCode
        );
    }
}