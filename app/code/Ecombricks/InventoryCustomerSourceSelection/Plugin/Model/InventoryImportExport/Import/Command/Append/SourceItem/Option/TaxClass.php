<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventoryImportExport\Import\Command\Append\SourceItem\Option;

/**
 * Source item tax class option import append command plugin
 */
class TaxClass extends \Ecombricks\InventoryCommon\Plugin\Model\InventoryImportExport\Import\Command\Append\SourceItem\Option
{
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Api\Inventory\SourceItem\Option\TaxClass\SaveInterface $saveOptions
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryImportExport\Import\SourceItem\Option\TaxClass\Convert $convertOptions
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Api\Inventory\SourceItem\Option\TaxClass\SaveInterface $saveOptions,
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryImportExport\Import\SourceItem\Option\TaxClass\Convert $convertOptions
    )
    {
        parent::__construct(
            $saveOptions,
            $convertOptions
        );
    }
}