<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\OfflineShipping\ResourceModel\Carrier\Tablerate\CSV;

/**
 * Shipping table rate CSV column resolver plugin
 */
class ColumnResolver extends \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\ColumnResolver
{
    /**
     * Source column
     */
    const COLUMN_SOURCE = 'Source';

    /**
     * Name to position ID map
     * 
     * @var array
     */
    private $nameToPositionIdMap = [
        self::COLUMN_SOURCE => 0,
        self::COLUMN_COUNTRY => 1,
        self::COLUMN_REGION => 2,
        self::COLUMN_ZIP => 3,
        self::COLUMN_WEIGHT => 4,
        self::COLUMN_WEIGHT_DESTINATION => 4,
        self::COLUMN_PRICE => 5,
    ];

    /**
     * Headers
     * 
     * @var array 
     */
    private $headers;

    /**
     * Constructor
     * 
     * @param array $headers
     * @param array $columns
     * @return void
     */
    public function __construct(array $headers, array $columns = [])
    {
        $this->nameToPositionIdMap = array_merge($this->nameToPositionIdMap, $columns);
        $this->headers = array_map('trim', $headers);
        parent::__construct($this->headers, $this->nameToPositionIdMap);
    }
    
    /**
     * Get source column index
     * 
     * @return int
     * @throws \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\ColumnNotFoundException
     */
    public function getSourceColumnIndex(): int
    {
        $columnIndex = array_search(self::COLUMN_SOURCE, $this->headers, true);
        if ($columnIndex !== false) {
            return $columnIndex;
        }
        if (array_key_exists(self::COLUMN_SOURCE, $this->nameToPositionIdMap)) {
            return $this->nameToPositionIdMap[self::COLUMN_SOURCE];
        }
        throw new \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\ColumnNotFoundException(
            __('Requested column "%1" cannot be resolved', self::COLUMN_SOURCE)
        );
    }
}