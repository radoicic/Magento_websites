<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales;

/**
 * Get product salable qty model
 */
class GetProductSalableQty implements \Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales\GetProductSalableQtyInterface
{
    /**
     * Get stock ID by source code
     * 
     * @var \Ecombricks\InventoryCommon\Model\GetStockIdBySourceCode 
     */
    private $getStockIdBySourceCode;

    /**
     * Get reservations quantity
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\GetReservationsQuantityInterface
     */
    private $getReservationsQuantity;

    /**
     * Get source items
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetSourceItems
     */
    private $getSourceItems;

    /**
     * Get stock item configuration
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\GetStockItemConfiguration
     */
    private $getStockItemConfiguration;
    
    /**
     * Get product types by SKUs
     * 
     * @var \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface
     */
    private $getProductTypesBySkus;

    /**
     * Is source item management allowed for product type
     * 
     * @var \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface 
     */
    private $isSourceItemManagementAllowedForProductType;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Model\GetStockIdBySourceCode $getStockIdBySourceCode
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\GetReservationsQuantityInterface $getReservationsQuantity
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetSourceItems $getSourceItems
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\GetStockItemConfiguration $getStockItemConfiguration
     * @param \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface $getProductTypesBySkus
     * @param \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Model\GetStockIdBySourceCode $getStockIdBySourceCode,
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\GetReservationsQuantityInterface $getReservationsQuantity,
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetSourceItems $getSourceItems,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\GetStockItemConfiguration $getStockItemConfiguration,
        \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface $getProductTypesBySkus,
        \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
    )
    {
        $this->getStockIdBySourceCode = $getStockIdBySourceCode;
        $this->getReservationsQuantity = $getReservationsQuantity;
        $this->getSourceItems = $getSourceItems;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
        $this->getProductTypesBySkus = $getProductTypesBySkus;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
    }

    /**
     * Execute
     * 
     * @param string $sku
     * @param string $sourceCode
     * @return float
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(string $sku, string $sourceCode): float
    {
        $this->validateProductType($sku);
        $stockId = $this->getStockIdBySourceCode->execute($sourceCode);
        $sourceItem = $this->getSourceItems->execute($sku, $stockId)[$sourceCode] ?? null;
        if (!$sourceItem || !$sourceItem->isSalable()) {
            return 0;
        }
        $stockItemConfig = $this->getStockItemConfiguration->execute($sku, $sourceCode);
        $qty = $sourceItem->getQuantity();
        $reservationQty = $this->getReservationsQuantity->execute($sku, $sourceCode);
        $minQty = $stockItemConfig->getMinQty();
        return $qty + $reservationQty - $minQty;
    }

    /**
     * Validate product type
     * 
     * @param string $sku
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     */
    private function validateProductType(string $sku): bool
    {
        $productTypes = $this->getProductTypesBySkus->execute([$sku]);
        if (!array_key_exists($sku, $productTypes)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('The product that was requested doesn\'t exist. Verify the product and try again.')
            );
        }
        $productType = $productTypes[$sku];
        if (false === $this->isSourceItemManagementAllowedForProductType->execute($productType)) {
            throw new \Magento\Framework\Exception\InputException(
                __('Can\'t check requested quantity for products without source items support.')
            );
        }
        return true;
    }
}