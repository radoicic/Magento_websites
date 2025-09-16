<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Api\Data;

/**
 * Source item option interface
 */
interface SourceItemOptionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Field keys
     */
    const SOURCE_CODE = 'source_code';
    const SKU = 'sku';
    const VALUE = 'value';

    /**
     * Get source code
     *
     * @return string|null
     */
    public function getSourceCode(): ?string;
    
    /**
     * Set source code
     * 
     * @param string $sourceCode
     * @return $this
     */
    public function setSourceCode(string $sourceCode);
    
    /**
     * Get SKU
     * 
     * @return string|null
     */
    public function getSku(): ?string;
    
    /**
     * Set SKU
     * 
     * @param string $sku
     * @return $this
     */
    public function setSku(string $sku);
    
    /**
     * Get value
     * 
     * @return mixed
     */
    public function getValue();
    
    /**
     * Set value
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value);
    
    /**
     * Get extension attributes
     *
     * @return \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\Ecombricks\InventoryCommon\Api\Data\SourceItemOptionExtensionInterface;
    
    /**
     * Set extension attributes
     *
     * @param \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Ecombricks\InventoryCommon\Api\Data\SourceItemOptionExtensionInterface $extensionAttributes);
}