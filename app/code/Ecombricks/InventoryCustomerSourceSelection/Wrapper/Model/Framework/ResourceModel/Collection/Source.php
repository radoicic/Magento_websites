<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Framework\ResourceModel\Collection;

/**
 * Collection source wrapper
 */
class Source extends \Ecombricks\Common\DataObject\Wrapper
{
    /**
     * Source table
     * 
     * @var string
     */
    private $sourceTable;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory
     * @param string $sourceTable
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory,
        string $sourceTable
    )
    {
        parent::__construct($objectReflectionFactory);
        $this->sourceTable = $sourceTable;
    }

    /**
     * Get source codes
     * 
     * @param array $modelIds
     * @return array
     */
    public function getSourceCodes(array $modelIds): array
    {
        $collection = $this->getObject();
        if (empty($modelIds)) {
            return [];
        }
        $connection = $collection->getConnection();
        $idFieldName = $collection->getResource()->getIdFieldName();
        $select = $connection->select()
            ->from($collection->getTable($this->sourceTable))
            ->where($idFieldName.' IN (?)', $modelIds);
        $sourceCodesData = $connection->fetchAll($select);
        $sourceCodes = [];
        if (!empty($sourceCodesData)) {
            foreach ($sourceCodesData as $sourceCodeData) {
                $modelId = $sourceCodeData[$idFieldName];
                $sourceCodes[$modelId] = $sourceCodeData['source_code'];
            }
        }
        return $sourceCodes;
    }
}