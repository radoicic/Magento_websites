<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model\ResourceModel\Source;

/**
 * Get source names
 */
class GetNames
{
    /**
     * Connection provider
     * 
     * @var \Ecombricks\Common\Model\ResourceModel\ConnectionProvider
     */
    private $connectionProvider;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider
    )
    {
        $this->connectionProvider = $connectionProvider;
    }
    
    /**
     * Execute
     * 
     * @param array $sourceCodes
     * @return array
     */
    public function execute(array $sourceCodes): array
    {
        $names = [];
        if (empty($sourceCodes)) {
            return $names;
        }
        $select = $this->connectionProvider->getSelect()
            ->from(
                $this->connectionProvider->getTable('inventory_source'),
                [ 'source_code', 'name' ]
            )
            ->where('source_code IN (?)', $sourceCodes);
        $rows = $this->connectionProvider->getConnection()->fetchAll($select);
        if (empty($rows)) {
            return $names;
        }
        foreach ($rows as $row) {
            $names[$row['source_code']] = $row['name'];
        }
        return $names;
    }
}