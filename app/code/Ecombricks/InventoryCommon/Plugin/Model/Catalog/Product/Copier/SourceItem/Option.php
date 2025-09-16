<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Plugin\Model\Catalog\Product\Copier\SourceItem;

/**
 * Product copier source item option plugin
 */
class Option
{
    /**
     * Get source item options
     * 
     * @var \Ecombricks\InventoryCommon\Api\SourceItem\Option\GetInterface
     */
    private $getOptions;

    /**
     * Save source item options
     * 
     * @var \Ecombricks\InventoryCommon\Api\SourceItem\Option\SaveInterface
     */
    private $saveOptions;

    /**
     * Source item option configuration
     * 
     * @var \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config
     */
    private $optionConfig;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Api\SourceItem\Option\GetInterface $getOptions
     * @param \Ecombricks\InventoryCommon\Api\SourceItem\Option\SaveInterface $saveOptions
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config $optionConfig
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Api\SourceItem\Option\GetInterface $getOptions,
        \Ecombricks\InventoryCommon\Api\SourceItem\Option\SaveInterface $saveOptions,
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config $optionConfig
    )
    {
        $this->getOptions = $getOptions;
        $this->saveOptions = $saveOptions;
        $this->optionConfig = $optionConfig;
    }

    /**
     * After copy
     *
     * @param \Magento\Catalog\Model\Product\Copier $subject
     * @param \Magento\Catalog\Model\Product $result
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product $result
     */
    public function afterCopy(
        \Magento\Catalog\Model\Product\Copier $subject,
        \Magento\Catalog\Model\Product $result,
        \Magento\Catalog\Model\Product $product
    )
    {
        if (!$this->optionConfig->isEnabled()) {
            return $result;
        }
        $this->copyOptions($product->getSku(), $result->getSku());
        return $result;
    }

    /**
     * Copy options
     *
     * @param string $origSku
     * @param string $sku
     * @return $this
     */
    private function copyOptions(string $origSku, string $sku)
    {
        $options = $this->getOptions->execute([$origSku])[$origSku] ?? [];
        foreach ($options as $option) {
            $option->setSku($sku);
        }
        if ($options) {
            $this->saveOptions->execute($options);
        }
        return $this;
    }
}