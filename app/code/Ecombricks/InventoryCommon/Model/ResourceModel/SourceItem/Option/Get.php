<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option;

/**
 * Get source item options resource
 */
class Get
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
     * @param array $skus
     * @param array|null $sourceCodes
     * @return array
     */
    public function execute(array $skus, array $sourceCodes = null): array
    {
        $select = $this->connectionProvider->getSelect()
            ->from($this->connectionProvider->getTable($this->tableName))
            ->where(\Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::SKU.' IN (?)', $skus)
            ->where(\Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::VALUE.' IS NOT NULL');
        if ($sourceCodes !== null) {
            $select->where(\Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::SOURCE_CODE.' IN (?)', $sourceCodes);
        }
        return $this->connectionProvider->getConnection()->fetchAll($select);
    }
}