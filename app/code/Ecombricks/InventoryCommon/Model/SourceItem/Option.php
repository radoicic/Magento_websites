<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model\SourceItem;

/**
 * Source item option
 */
class Option extends \Magento\Framework\Model\AbstractExtensibleModel implements \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface
{
    /**
     * Get source code
     * 
     * @return string|null
     */
    public function getSourceCode(): ?string
    {
        return $this->getData(static::SOURCE_CODE);
    }
    
    /**
     * Set source code
     * 
     * @param string $sourceCode
     * @return $this
     */
    public function setSourceCode(string $sourceCode)
    {
        $this->setData(static::SOURCE_CODE, $sourceCode);
        return $this;
    }
    
    /**
     * Get SKU
     * 
     * @return string|null
     */
    public function getSku(): ?string
    {
        return $this->getData(static::SKU);
    }
    
    /**
     * Set SKU
     * 
     * @param string $sku
     * @return $this
     */
    public function setSku(string $sku)
    {
        $this->setData(static::SKU, $sku);
        return $this;
    }
    
    /**
     * Get value
     * 
     * @return mixed
     */
    public function getValue()
    {
        return $this->getData(static::VALUE);
    }
    
    /**
     * Set value
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->setData(static::VALUE, $value);
        return $this;
    }
    
    /**
     * Get extension attributes
     *
     * @return \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\Ecombricks\InventoryCommon\Api\Data\SourceItemOptionExtensionInterface
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->extensionAttributesFactory->create(\Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::class);
            $this->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }
    
    /**
     * Set extension attributes
     *
     * @param \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Ecombricks\InventoryCommon\Api\Data\SourceItemOptionExtensionInterface $extensionAttributes)
    {
        $this->_setExtensionAttributes($extensionAttributes);
        return $this;
    }
}