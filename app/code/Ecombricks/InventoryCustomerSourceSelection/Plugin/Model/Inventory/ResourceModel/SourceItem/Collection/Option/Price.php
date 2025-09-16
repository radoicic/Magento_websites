<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Inventory\ResourceModel\SourceItem\Collection\Option;

/**
 * Source item collection price option plugin
 */
class Price extends \Ecombricks\InventoryCommon\Plugin\Model\Inventory\ResourceModel\SourceItem\Collection\Option
{
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Meta $optionMeta
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Meta $optionMeta
    )
    {
        parent::__construct($optionMeta);
    }
}