<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales;

/**
 * Source item
 */
class SourceItem implements \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\SourceItemInterface
{
    /**
     * SKU
     * 
     * @var string
     */
    private $sku;
    
    /**
     * Source code
     * 
     * @var string
     */
    private $sourceCode;
    
    /**
     * Quantity
     * 
     * @var float
     */
    private $quantity;
    
    /**
     * Is salable
     * 
     * @var bool
     */
    private $isSalable;
    
    /**
     * Get SKU
     * 
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }
    
    /**
     * Set SKU
     * 
     * @param string $sku
     * @return $this
     */
    public function setSku(string $sku)
    {
        $this->sku = $sku;
        return $this;
    }
    
    /**
     * Get source code
     * 
     * @return string
     */
    public function getSourceCode(): string
    {
        return $this->sourceCode;
    }
    
    /**
     * Set source code
     * 
     * @param string $sourceCode
     * @return $this
     */
    public function setSourceCode(string $sourceCode)
    {
        $this->sourceCode = $sourceCode;
        return $this;
    }
    
    /**
     * Get quantity
     * 
     * @return float
     */
    public function getQuantity(): float
    {
        return $this->quantity;
    }

    /**
     * Set quantity
     * 
     * @param float $quantity
     * @return $this
     */
    public function setQuantity(float $quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }
    
    /**
     * Get is salable
     * 
     * @return float
     */
    public function getIsSalable(): bool
    {
        return $this->isSalable;
    }

    /**
     * Set is salable
     * 
     * @param bool $isSalable
     * @return $this
     */
    public function setIsSalable(bool $isSalable)
    {
        $this->isSalable = $isSalable;
        return $this;
    }
    
    /**
     * Check if is salable
     * 
     * @return float
     */
    public function isSalable(): bool
    {
        return $this->getIsSalable();
    }
    
    /**
     * To array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'sku' => $this->getSku(),
            'source_code' => $this->getSourceCode(),
            'quantity' => $this->getQuantity(),
            'is_salable' => $this->getIsSalable(),
        ];
    }
}