<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model\SourceItem\Option;

/**
 * Get source item options
 */
class Get implements \Ecombricks\InventoryCommon\Api\SourceItem\Option\GetInterface
{
    /**
     * Resource
     * 
     * @var \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Get
     */
    private $resource;

    /**
     * Source item option factory
     * 
     * @var \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterfaceFactory
     */
    private $optionFactory;

    /**
     * Data object helper
     * 
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * Source item option meta
     * 
     * @var \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta
     */
    private $optionMeta;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Get $resource
     * @param \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterfaceFactory $optionFactory
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta $optionMeta
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option\Get $resource,
        \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterfaceFactory $optionFactory,
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta $optionMeta,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
    )
    {
        $this->resource = $resource;
        $this->optionFactory = $optionFactory;
        $this->optionMeta = $optionMeta;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Execute
     * 
     * @param array $skus
     * @param array|null $sourceCodes
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(array $skus, array $sourceCodes = null): array
    {
        if (empty($skus)) {
            throw new \Magento\Framework\Exception\InputException(__('No SKUs provided for the %1.', $this->optionMeta->getLabel()));
        }
        $optionsData = $this->resource->execute($skus, $sourceCodes);
        if (empty($optionsData)) {
            return [];
        }
        $options = [];
        foreach ($optionsData as $optionData) {
            if (isset($optionData['value']) && $optionData['value'] === null) {
                continue;
            }
            $option = $this->optionFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $option,
                $optionData,
                \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::class
            );
            $options[$option->getSku()][$option->getSourceCode()] = $option;
        }
        return $options;
    }
}