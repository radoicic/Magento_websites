<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Plugin\Model\InventoryImportExport\Import\Command\Replace\SourceItem;

/**
 * Source item option import replace command plugin
 */
class Option
{
    /**
     * Delete options
     * 
     * @var \Ecombricks\InventoryCommon\Api\SourceItem\Option\DeleteInterface
     */
    private $deleteOptions;

    /**
     * Save options
     * 
     * @var \Ecombricks\InventoryCommon\Api\SourceItem\Option\SaveInterface
     */
    private $saveOptions;

    /**
     * Convert options
     * 
     * @var \Ecombricks\InventoryCommon\Model\Import\SourceItem\Option\Convert
     */
    private $convertOptions;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Api\SourceItem\Option\DeleteInterface $deleteOptions
     * @param \Ecombricks\InventoryCommon\Api\SourceItem\Option\SaveInterface $saveOptions
     * @param \Ecombricks\InventoryCommon\Model\Import\SourceItem\Option\Convert $convertOptions
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Api\SourceItem\Option\DeleteInterface $deleteOptions,
        \Ecombricks\InventoryCommon\Api\SourceItem\Option\SaveInterface $saveOptions,
        \Ecombricks\InventoryCommon\Model\Import\SourceItem\Option\Convert $convertOptions
    )
    {
        $this->deleteOptions = $deleteOptions;
        $this->saveOptions = $saveOptions;
        $this->convertOptions = $convertOptions;
    }

    /**
     * Around execute
     * 
     * @param \Magento\InventoryImportExport\Model\Import\Command\Replace $subject
     * @param callable $proceed
     * @param array $bunch
     * @return void
     */
    public function aroundExecute(
        \Magento\InventoryImportExport\Model\Import\Command\Replace $subject,
        callable $proceed,
        array $bunch
    )
    {
        $options = $this->convertOptions->convert($bunch);
        $proceed($bunch);
        $this->deleteOptions->execute($options);
        $this->saveOptions->execute($options);
    }
}