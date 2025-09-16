<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model\Import\SourceItem\Option;

/**
 * Source item option convert
 */
class Convert
{
    /**
     * Option factory
     * 
     * @var \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterfaceFactory
     */
    private $optionFactory;

    /**
     * Source item option meta
     * 
     * @var \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta
     */
    private $optionMeta;

    /**
     * Data object helper
     * 
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterfaceFactory $optionFactory
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta $optionMeta
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterfaceFactory $optionFactory,
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta $optionMeta,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
    )
    {
        $this->optionFactory = $optionFactory;
        $this->optionMeta = $optionMeta;
        $this->dataObjectHelper = $dataObjectHelper;
    }
    
    /**
     * Convert
     * 
     * @param array $bunch
     * @return \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface[]
     */
    public function convert(array $bunch): array
    {
        $optionName = $this->optionMeta->getName();
        $options = [];
        foreach ($bunch as $optionData) {
            $optionData[\Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::VALUE] = $optionData[$optionName] ?? null;
            $option = $this->optionFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $option,
                $optionData,
                \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::class
            );
            $options[] = $option;
        }
        return $options;
    }
}