<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option;

/**
 * Delete source item options resource
 */
class Delete
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
        if (empty($options)) {
            return $this;
        }
        $this->connectionProvider->getConnection()->delete(
            $this->connectionProvider->getTable($this->tableName),
            $this->getWhereSql($options)
        );
        return $this;
    }

    /**
     * Get where SQL
     *
     * @param \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface[] $options
     * @return string
     */
    private function getWhereSql(array $options): string
    {
        $connection = $this->connectionProvider->getConnection();
        $subConditions = [];
        foreach ($options as $option) {
            $subConditions[] = $this->connectionProvider->getCondition([
                $connection->quoteInto(
                    \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::SKU.' = ?',
                    $option->getSku()
                ),
                $connection->quoteInto(
                    \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::SOURCE_CODE.' = ?',
                    $option->getSourceCode()
                ),
            ], 'AND');
        }
        return $this->connectionProvider->getCondition($subConditions, 'OR');
    }
}