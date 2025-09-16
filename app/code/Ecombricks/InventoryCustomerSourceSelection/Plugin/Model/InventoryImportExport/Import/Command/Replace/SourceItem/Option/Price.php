<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventoryImportExport\Import\Command\Replace\SourceItem\Option;

/**
 * Source item price option import replace command plugin
 */
class Price extends \Ecombricks\InventoryCommon\Plugin\Model\InventoryImportExport\Import\Command\Replace\SourceItem\Option
{
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Api\Inventory\SourceItem\Option\Price\DeleteInterface $deleteOptions
     * @param \Ecombricks\InventoryCustomerSourceSelection\Api\Inventory\SourceItem\Option\Price\SaveInterface $saveOptions
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryImportExport\Import\SourceItem\Option\Price\Convert $convertOptions
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Api\Inventory\SourceItem\Option\Price\DeleteInterface $deleteOptions,
        \Ecombricks\InventoryCustomerSourceSelection\Api\Inventory\SourceItem\Option\Price\SaveInterface $saveOptions,
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryImportExport\Import\SourceItem\Option\Price\Convert $convertOptions
    )
    {
        parent::__construct(
            $deleteOptions,
            $saveOptions,
            $convertOptions
        );
    }
}