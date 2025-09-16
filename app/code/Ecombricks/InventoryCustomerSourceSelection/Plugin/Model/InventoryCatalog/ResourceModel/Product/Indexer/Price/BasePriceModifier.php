<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventoryCatalog\ResourceModel\Product\Indexer\Price;

/**
 * Base product price indexer modifier plugin
 */
class BasePriceModifier
{
    /**
     * Source price modifier
     *
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryCatalog\ResourceModel\Product\Indexer\Price\SourcePriceModifier
     */
    private $sourcePriceModifier;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryCatalog\ResourceModel\Product\Indexer\Price\SourcePriceModifier $sourcePriceModifier
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryCatalog\ResourceModel\Product\Indexer\Price\SourcePriceModifier $sourcePriceModifier
    )
    {
        $this->sourcePriceModifier = $sourcePriceModifier;
    }

    /**
     * Around modify price
     * 
     * @param \Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\BasePriceModifier $subject
     * @param callable $proceed
     * @param \Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\IndexTableStructure $priceTable
     * @param array $entityIds
     * @return void
     */
    public function aroundModifyPrice(
        \Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\BasePriceModifier $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\IndexTableStructure $priceTable,
        array $entityIds = []
    ): void
    {
        $this->sourcePriceModifier->modifyPrice($priceTable, $entityIds);
        $proceed($priceTable, $entityIds);
    }
}