<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventoryImportExport\Export\AttributeCollectionProvider\SourceItem\Option;

/**
 * Source item price option export attribute collection provider plugin
 */
class Price extends \Ecombricks\InventoryCommon\Plugin\Model\InventoryImportExport\Export\AttributeCollectionProvider\SourceItem\Option
{
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Meta $optionMeta
     * @param \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Meta $optionMeta,
        \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory
    )
    {
        parent::__construct(
            $optionMeta,
            $attributeFactory
        );
    }
}