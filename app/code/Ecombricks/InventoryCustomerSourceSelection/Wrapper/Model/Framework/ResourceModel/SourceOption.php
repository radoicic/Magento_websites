<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Framework\ResourceModel;

/**
 * Resource source option wrapper
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
     * @param mixed $modelId
     * @return array
     */
    public function getSourceOptions($modelId): array
    {
        if (empty($modelId)) {
            return [];
        }
        $resource = $this->getObject();
        $connection = $resource->getConnection();
        $select = $connection->select()
            ->from($resource->getTable($this->sourceOptionTable))
            ->where($resource->getIdFieldName().' = ?', $modelId);
        $sourceOptionsData = $connection->fetchAll($select);
        if (empty($sourceOptionsData)) {
            return [];
        }
        $sourceOptions = [];
        foreach ($sourceOptionsData as $sourceOptionData) {
            $sourceOptions[$sourceOptionData['source_code']] = $sourceOptionData['value'];
        }
        return $sourceOptions;
    }

    /**
     * Delete source options
     * 
     * @param mixed $modelId
     * @return void
     */
    public function deleteSourceOptions($modelId): void
    {
        if (empty($modelId)) {
            return;
        }
        $resource = $this->getObject();
        $resource->getConnection()->delete(
            $resource->getTable($this->sourceOptionTable),
            [ $resource->getIdFieldName().' = ?' => $modelId ]
        );
    }

    /**
     * Save source options
     * 
     * @param mixed $modelId
     * @param array $sourceOptions
     * @return void
     */
    public function saveSourceOptions($modelId, array $sourceOptions): void
    {
        if (empty($modelId)) {
            return;
        }
        $resource = $this->getObject();
        $connection = $resource->getConnection();
        $origSourceOptions = $this->getSourceOptions($modelId);
        $origSourceCodes = array_keys($origSourceOptions);
        $sourceCodes = array_keys($sourceOptions);
        $sourceCodesToDelete = array_diff($origSourceCodes, $sourceCodes);
        $sourceCodesToInsert = array_diff($sourceCodes, $origSourceCodes);
        $sourceCodesToUpdate = array_intersect($origSourceCodes, $sourceCodes);
        if (!empty($sourceCodesToDelete)) {
            $connection->delete(
                $resource->getTable($this->sourceOptionTable),
                [
                    $resource->getIdFieldName().' = ?' => $modelId,
                    'source_code IN (?)' => $sourceCodesToDelete,
                ]
            );
        }
        if (!empty($sourceCodesToInsert)) {
            $sourceOptionsData = [];
            foreach ($sourceCodesToInsert as $sourceCode) {
                $sourceOptionsData[] = [
                    $resource->getIdFieldName() => $modelId,
                    'source_code' => $sourceCode,
                    'value' => $sourceOptions[$sourceCode],
                ];
            }
            $connection->insertMultiple(
                $resource->getTable($this->sourceOptionTable),
                $sourceOptionsData
            );
        }
        if (!empty($sourceCodesToUpdate)) {
            foreach ($sourceCodesToUpdate as $sourceCode) {
                $connection->update(
                    $resource->getTable($this->sourceOptionTable),
                    [
                        'value' => $sourceOptions[$sourceCode],
                    ],
                    [
                        $resource->getIdFieldName().' = ?' => $modelId,
                        'source_code = ?' => $sourceCode,
                    ]
                );
            }
        }
    }
}