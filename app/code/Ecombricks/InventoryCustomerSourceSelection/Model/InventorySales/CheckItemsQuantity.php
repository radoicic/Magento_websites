<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales;

/**
 * Check items quantity model
 */
class CheckItemsQuantity
{
    /**
     * Is product salable for requested qty
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales\IsProductSalableForRequestedQtyInterface
     */
    private $isProductSalableForRequestedQty;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales\IsProductSalableForRequestedQtyInterface $isProductSalableForRequestedQty
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales\IsProductSalableForRequestedQtyInterface $isProductSalableForRequestedQty
    )
    {
        $this->isProductSalableForRequestedQty = $isProductSalableForRequestedQty;
    }
    
    /**
     * Execute
     * 
     * @param array $items
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(array $items): void
    {
        foreach ($items as $sku => $sourceQtys) {
            foreach ($sourceQtys as $sourceCode => $qty) {
                $isSalable = $this->isProductSalableForRequestedQty->execute((string) $sku, (string) $sourceCode, (float) $qty);
                if (false === $isSalable->isSalable()) {
                    $errors = $isSalable->getErrors();
                    $errorMessage = array_pop($errors);
                    throw new \Magento\Framework\Exception\LocalizedException(__($errorMessage->getMessage()));
                }
            }
        }
    }
}