<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Framework\ResourceModel\Collection;

/**
 * Collection source option wrapper
 */
class SourceOption extends \Ecombricks\Common\DataObject\Wrapper
{
    /**
     * Source item option
     * 
     * @var string
     */
    private $sourceOptionTable;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory
     * @param string $sourceOptionTable
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory,
        string $sourceOptionTable
    )
    {
        parent::__construct($objectReflectionFactory);
        $this->sourceOptionTable = $sourceOptionTable;
    }

    /**
     * Get source options
     * 
     * @param array $modelIds
     * @return array
     */
    public function getSourceOptions(array $modelIds): array
    {
        $collection = $this->getObject();
        if (empty($modelIds)) {
            return [];
        }
        $connection = $collection->getConnection();
        $idFieldName = $collection->getResource()->getIdFieldName();
        $select = $connection->select()
            ->from($collection->getTable($this->sourceOptionTable))
            ->where($idFieldName.' IN (?)', $modelIds);
        $sourceOptions = [];
        $sourceOptionsData = $connection->fetchAll($select);
        if (!empty($sourceOptionsData)) {
            foreach ($sourceOptionsData as $sourceOptionData) {
                $modelId = $sourceOptionData[$idFieldName];
                $sourceCode = $sourceOptionData['source_code'];
                $sourceOptions[$modelId][$sourceCode] = $sourceOptionData['value'];
            }
        }
        return $sourceOptions;
    }
}