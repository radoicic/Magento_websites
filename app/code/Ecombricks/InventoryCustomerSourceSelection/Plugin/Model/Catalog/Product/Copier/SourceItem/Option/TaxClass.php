<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Catalog\Product\Copier\SourceItem\Option;

/**
 * Product resource source item tax class options plugin
 */
class TaxClass extends \Ecombricks\InventoryCommon\Plugin\Model\Catalog\Product\Copier\SourceItem\Option
{
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Api\Inventory\SourceItem\Option\TaxClass\GetInterface $getOptions
     * @param \Ecombricks\InventoryCustomerSourceSelection\Api\Inventory\SourceItem\Option\TaxClass\SaveInterface $saveOptions
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Config $optionConfig
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Api\Inventory\SourceItem\Option\TaxClass\GetInterface $getOptions,
        \Ecombricks\InventoryCustomerSourceSelection\Api\Inventory\SourceItem\Option\TaxClass\SaveInterface $saveOptions,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Config $optionConfig
    )
    {
        parent::__construct(
            $getOptions,
            $saveOptions,
            $optionConfig
        );
    }
    
}