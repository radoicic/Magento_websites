<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\CatalogInventory;

/**
 * Stock management
 */
class StockState extends \Magento\CatalogInventory\Model\StockState 
    implements \Ecombricks\InventoryCustomerSourceSelection\Api\CatalogInventory\StockStateInterface 
{
    /**
     * Object factory
     * 
     * @var \Magento\Framework\DataObject\Factory
     */
    private $objectFactory;

    /**
     * Constructor
     * 
     * @param \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface $stockStateProvider
     * @param \Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface $stockRegistryProvider
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\DataObject\Factory $objectFactory
     * @return void
     */
    public function __construct(
        \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface $stockStateProvider,
        \Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface $stockRegistryProvider,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\DataObject\Factory $objectFactory
    )
    {
        parent::__construct(
            $stockStateProvider,
            $stockRegistryProvider,
            $stockConfiguration
        );
        $this->objectFactory = $objectFactory;
    }
    
    /**
     * Check quote item source qty
     *
     * @param int $productId
     * @param string $sourceCode
     * @param float $itemQty
     * @param float $qtyToCheck
     * @param float $origQty
     * @return \Magento\Framework\DataObject
     */
    public function checkQuoteItemSourceQty($productId, $sourceCode, $itemQty, $qtyToCheck, $origQty)
    {
        return $this->objectFactory->create()->setHasError(false);
    }
    
    /**
     * Suggest source qty
     *
     * @param int $productId
     * @param string $sourceCode
     * @param float $qty
     * @return float
     */
    public function suggestSourceQty($productId, $sourceCode, $qty)
    {
        return $qty;
    }
}