<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option;

/**
 * Save source item options resource
 */
class Save
{
    /**
     * Connection provider
     * 
     * @var \Ecombricks\Common\Model\ResourceModel\ConnectionProvider
     */
    private $connectionProvider;

    /**
     * Table name
     * 
     * @var string
     */
    private $tableName;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider
     * @param string $tableName
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider,
        string $tableName
    )
    {
        $this->connectionProvider = $connectionProvider;
        $this->tableName = $tableName;
    }

    /**
     * Execute
     * 
     * @param \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface[] $options
     * @return $this
     */
    public function execute(array $options)
    {
        if (!count($options)) {
            return $this;
        }
        $optionsData = [];
        foreach ($options as $option) {
            $optionsData[] = [
                \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::SOURCE_CODE => $option->getSourceCode(),
                \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::SKU => $option->getSku(),
                \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::VALUE => $option->getValue(),
            ];
        }
        $this->connectionProvider->getConnection()->insertOnDuplicate(
            $this->connectionProvider->getTable($this->tableName),
            $optionsData
        );
        return $this;
    }
}
