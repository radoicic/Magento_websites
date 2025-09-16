<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventoryImportExport\Import\SourceItem\Option\TaxClass;

/**
 * Source item tax class option convert
 */
class Convert extends \Ecombricks\InventoryCommon\Model\Import\SourceItem\Option\Convert
{
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterfaceFactory $optionFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Meta $optionMeta
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterfaceFactory $optionFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass\Meta $optionMeta,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
    )
    {
        parent::__construct(
            $optionFactory,
            $optionMeta,
            $dataObjectHelper
        );
    }
}