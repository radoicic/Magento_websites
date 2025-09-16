<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Helper\Catalog\Product;

/**
 * Product configuration helper plugin
 */
class Configuration
{
    /**
     * Get source by source code
     * 
     * @var \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode
     */
    private $getSourceBySourceCode;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode $getSourceBySourceCode
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode $getSourceBySourceCode
    )
    {
        $this->getSourceBySourceCode = $getSourceBySourceCode;
    }

    /**
     * After get custom options
     * 
     * @param \Magento\Catalog\Helper\Product\Configuration $subject
     * @param array $result
     * @param \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item
     * @return array
     */
    public function afterGetCustomOptions(
        \Magento\Catalog\Helper\Product\Configuration $subject,
        $result,
        \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item
    )
    {
        $sourceOption = $item->getOptionByCode('source');
        if (empty($sourceOption)) {
            return $result;
        }
        $source = $this->getSourceBySourceCode->execute($sourceOption->getValue());
        if (empty($source)) {
            return $result;
        }
        $result[] = [
            'label' => __('Source'),
            'value' => $source->getName(),
            'type' => 'source',
            'source_code' => $source->getSourceCode(),
        ];
        return $result;
    }
}