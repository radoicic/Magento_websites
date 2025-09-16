<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Plugin\Model\InventoryImportExport\Export\AttributeCollectionProvider\SourceItem;

/**
 * Source item option export attribute collection provider plugin
 */
class Option
{
    /**
     * Source item option meta
     * 
     * @var \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta
     */
    private $optionMeta;

    /**
     * Attribute factory
     * 
     * @var \Magento\Eav\Model\Entity\AttributeFactory
     */
    private $attributeFactory;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta $optionMeta
     * @param \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta $optionMeta,
        \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory
    )
    {
        $this->optionMeta = $optionMeta;
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * After get
     * 
     * @param \Magento\InventoryImportExport\Model\Export\AttributeCollectionProvider $subject
     * @param \Magento\Framework\Data\Collection $result
     * @return \Magento\Framework\Data\Collection
     */
    public function afterGet(
        \Magento\InventoryImportExport\Model\Export\AttributeCollectionProvider $subject,
        \Magento\Framework\Data\Collection $result
    ): \Magento\Framework\Data\Collection
    {
        if ($this->isAttributeAdded($result)) {
            return $result;
        }
        $this->addAttribute($result);
        return $result;
    }
    
    /**
     * Add attribute
     * 
     * @param \Magento\Framework\Data\Collection $collection
     * @return void
     */
    private function addAttribute(\Magento\Framework\Data\Collection $collection): void
    {
        $attributeCode = $this->optionMeta->getName();
        $attribute = $this->attributeFactory->create();
        $attribute->setId($attributeCode);
        $attribute->setDefaultFrontendLabel($attributeCode);
        $attribute->setAttributeCode($attributeCode);
        $attribute->setBackendType($this->optionMeta->getBackendType());
        $frontendInput = $this->optionMeta->getFrontendInput();
        if ($frontendInput !== null) {
            $attribute->setFrontendInput($frontendInput);
        }
        $sourceModel = $this->optionMeta->getSourceModel();
        if ($sourceModel !== null) {
            $attribute->setSourceModel($sourceModel);
        }
        $collection->addItem($attribute);
    }

    /**
     * Check if attribute is added
     * 
     * @param \Magento\Framework\Data\Collection $collection
     * @return bool
     */
    private function isAttributeAdded(\Magento\Framework\Data\Collection $collection): bool
    {
        $attributeCode = $this->optionMeta->getName();
        return count(
            array_filter(
                $collection->getItems(),
                function ($attribute) use ($attributeCode) {
                    return $attribute->getAttributeCode() === $attributeCode;
                }
            )
        ) > 0;
    }
}