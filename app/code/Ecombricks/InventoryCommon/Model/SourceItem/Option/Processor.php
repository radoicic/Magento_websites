<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model\SourceItem\Option;

/**
 * Source item options processor
 */
class Processor
{
    /**
     * Source item option factory
     * 
     * @var \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterfaceFactory
     */
    private $optionFactory;

    /**
     * Save source item options
     * 
     * @var \Ecombricks\InventoryCommon\Api\SourceItem\Option\SaveInterface
     */
    private $saveOptions;

    /**
     * Delete source item options
     * 
     * @var \Ecombricks\InventoryCommon\Api\SourceItem\Option\DeleteInterface
     */
    private $deleteOptions;

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
     * @param \Ecombricks\InventoryCommon\Api\SourceItem\Option\SaveInterface $saveOptions
     * @param \Ecombricks\InventoryCommon\Api\SourceItem\Option\DeleteInterface $deleteOptions
     * @param \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta $optionMeta
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterfaceFactory $optionFactory,
        \Ecombricks\InventoryCommon\Api\SourceItem\Option\SaveInterface $saveOptions,
        \Ecombricks\InventoryCommon\Api\SourceItem\Option\DeleteInterface $deleteOptions,
        \Ecombricks\InventoryCommon\Model\SourceItem\Option\Meta $optionMeta,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
    )
    {
        $this->optionFactory = $optionFactory;
        $this->saveOptions = $saveOptions;
        $this->deleteOptions = $deleteOptions;
        $this->optionMeta = $optionMeta;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Execute
     * 
     * @param string $sku
     * @param array $optionsData
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     */
    public function execute($sku, array $optionsData)
    {
        $options = [];
        foreach ($optionsData as $optionData) {
            $this->validateOptionData($optionData);
            $option = $this->optionFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $option,
                $this->prepareOptionData($sku, $optionData),
                \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::class
            );
            $options[] = $option;
        }
        if (empty($options)) {
            return $this;
        }
        $this->deleteOptions->execute($options);
        $this->saveOptions->execute($options);
        return $this;
    }

    /**
     * Validate option data
     * 
     * @param array $optionData
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     */
    private function validateOptionData(array $optionData)
    {
        if (!isset($optionData[\Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::SOURCE_CODE])) {
            throw new \Magento\Framework\Exception\InputException(__('No source code provided for the %1.', $this->optionMeta->getLabel()));
        }
        return $this;
    }

    /**
     * Prepare option data
     * 
     * @param string $sku
     * @param array $optionData
     * @return array
     */
    private function prepareOptionData(string $sku, array $optionData): array
    {
        $optionName = $this->optionMeta->getName();
        $optionData[\Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::SKU] = $sku;
        $optionData[\Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::VALUE] = $optionData[$optionName];
        if ($optionData[$optionName.'_use_default']) {
            unset($optionData[\Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::VALUE]);
        }
        return $optionData;
    }
}