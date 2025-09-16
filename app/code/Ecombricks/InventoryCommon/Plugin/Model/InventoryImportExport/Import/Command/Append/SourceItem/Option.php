<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Plugin\Model\InventoryImportExport\Import\Command\Append\SourceItem;

/**
 * Source item option import append command plugin
 */
class Option
{
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
     * @param \Ecombricks\InventoryCommon\Api\SourceItem\Option\SaveInterface $saveOptions
     * @param \Ecombricks\InventoryCommon\Model\Import\SourceItem\Option\Convert $convertOptions
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Api\SourceItem\Option\SaveInterface $saveOptions,
        \Ecombricks\InventoryCommon\Model\Import\SourceItem\Option\Convert $convertOptions
    )
    {
        $this->saveOptions = $saveOptions;
        $this->convertOptions = $convertOptions;
    }

    /**
     * Around execute
     * 
     * @param \Magento\InventoryImportExport\Model\Import\Command\Append $subject
     * @param callable $proceed
     * @param array $bunch
     * @return void
     */
    public function aroundExecute(
        \Magento\InventoryImportExport\Model\Import\Command\Append $subject,
        callable $proceed,
        array $bunch
    )
    {
        $options = $this->convertOptions->convert($bunch);
        $proceed($bunch);
        $this->saveOptions->execute($options);
    }
}