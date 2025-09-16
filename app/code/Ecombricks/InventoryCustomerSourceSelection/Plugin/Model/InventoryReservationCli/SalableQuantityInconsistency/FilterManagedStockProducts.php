<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventoryReservationCli\SalableQuantityInconsistency;

/**
 * Filter managed stock products plugin
 */
class FilterManagedStockProducts
{
    /**
     * Is product assigned to source
     * 
     * @var \Ecombricks\InventoryCommon\Model\ResourceModel\IsProductAssignedToSource
     */
    private $isProductAssignedToSource;

    /**
     * Get stock item configuration
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\GetStockItemConfiguration
     */
    private $getStockItemConfiguration;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Model\ResourceModel\IsProductAssignedToSource $isProductAssignedToSource
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\GetStockItemConfiguration $getStockItemConfiguration
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Model\ResourceModel\IsProductAssignedToSource $isProductAssignedToSource,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\GetStockItemConfiguration $getStockItemConfiguration
    )
    {
        $this->isProductAssignedToSource = $isProductAssignedToSource;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
    }

    /**
     * Around execute
     * 
     * @param \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\FilterManagedStockProducts $subject
     * @param callable $proceed
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservationCli\SalableQuantityInconsistency[] $inconsistencies
     * @return \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservationCli\SalableQuantityInconsistency[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundExecute(
        \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\FilterManagedStockProducts $subject,
        callable $proceed,
        array $inconsistencies
    ): array
    {
        foreach ($inconsistencies as $inconsistency) {
            $filteredItems = [];
            foreach ($inconsistency->getItems() as $sku => $qty) {
                $sourceCode = (string) $inconsistency->getSourceCode();
                if (false === $this->isProductAssignedToSource->execute((string) $sku, $sourceCode)) {
                    continue;
                }
                $stockConfiguration = $this->getStockItemConfiguration->execute((string) $sku, $sourceCode);
                if (!$stockConfiguration->isManageStock()) {
                    continue;
                }
                $filteredItems[$sku] = $qty;
            }
            $inconsistency->setItems($filteredItems);
        }
        return $inconsistencies;
    }
}