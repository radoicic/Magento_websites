<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\OfflineShipping\ResourceModel\Carrier\Tablerate\CSV;

/**
 * Shipping tablerate CSV row parser plugin
 */
class RowParser
{
    /**
     * Product metadata
     * 
     * @var \Magento\Framework\App\ProductMetadata 
     */
    private $productMetadata;

    /**
     * Constructor
     * 
     * @param \Magento\Framework\App\ProductMetadata $productMetadata
     * @return void
     */
    public function __construct(\Magento\Framework\App\ProductMetadata $productMetadata)
    {
        $this->productMetadata = $productMetadata;
    }
    
    /**
     * After get columns
     * 
     * @param \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\RowParser $subject
     * @param array $result
     * @return array
     */
    public function afterGetColumns(
        \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\RowParser $subject,
        $result
    )
    {
        array_unshift($result, 'source_code');
        return $result;
    }

    /**
     * After parse
     * 
     * @param \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\RowParser $subject
     * @param array $result
     * @param array $rowData
     * @param int $rowNumber
     * @param int $websiteId
     * @param string $conditionShortName
     * @param string $conditionFullName
     * @param \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\ColumnResolver $columnResolver
     * @return array
     * @throws \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\ColumnNotFoundException
     * @throws \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\RowException
     */
    public function afterParse(
        \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\RowParser $subject,
        $result,
        array $rowData,
        $rowNumber,
        $websiteId,
        $conditionShortName,
        $conditionFullName,
        \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\ColumnResolver $columnResolver
    )
    {
        $sourceColumnIndex = $columnResolver->getSourceColumnIndex();
        if (!array_key_exists($sourceColumnIndex, $rowData)) {
            throw new \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\ColumnNotFoundException(
                __('Column "%1" not found', \Ecombricks\InventoryCustomerSourceSelection\Model\OfflineShipping\ResourceModel\Carrier\Tablerate\CSV\ColumnResolver::COLUMN_SOURCE)
            );
        }
        $sourceCode = trim((string) $rowData[$sourceColumnIndex]);
        unset($rowData[$sourceColumnIndex]);
        if (version_compare($this->productMetadata->getVersion(), '2.3.1', '>=')) {
            $modifiedResult = [];
            foreach ($result as $rate) {
                $modifiedResult[] = array_merge(['source_code' => $sourceCode !== '' ? $sourceCode : null], $rate);
            }
            unset($result);
            return $modifiedResult;
        } else {
            return array_merge(['source_code' => $sourceCode !== '' ? $sourceCode : null], $result);
        }
    }
}