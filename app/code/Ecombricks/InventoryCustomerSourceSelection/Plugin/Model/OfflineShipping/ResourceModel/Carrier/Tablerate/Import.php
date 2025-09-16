<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\OfflineShipping\ResourceModel\Carrier\Tablerate;

/**
 * Shipping table rate import plugin
 */
class Import
{
    /**
     * Column resolver factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\OfflineShipping\ResourceModel\Carrier\Tablerate\CSV\ColumnResolverFactory
     */
    private $columnResolverFactory;

    /**
     * Product metadata
     * 
     * @var \Magento\Framework\App\ProductMetadata 
     */
    private $productMetadata;

    /**
     * Row parser
     * 
     * @var \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\RowParser 
     */
    private $rowParser;

    /**
     * Data hash generator
     * 
     * @var \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\DataHashGenerator
     */
    private $dataHashGenerator;

    /**
     * Errors
     * 
     * @var array
     */
    private $errors = [];

    /**
     * Unique hash
     * 
     * @var array
     */
    private $uniqueHash = [];

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\OfflineShipping\ResourceModel\Carrier\Tablerate\CSV\ColumnResolverFactory $columnResolverFactory
     * @param \Magento\Framework\App\ProductMetadata $productMetadata
     * @param \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\RowParser $rowParser
     * @param \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\DataHashGenerator $dataHashGenerator
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\OfflineShipping\ResourceModel\Carrier\Tablerate\CSV\ColumnResolverFactory $columnResolverFactory,
        \Magento\Framework\App\ProductMetadata $productMetadata,
        \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\RowParser $rowParser,
        \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\DataHashGenerator $dataHashGenerator
    )
    {
        $this->columnResolverFactory = $columnResolverFactory;
        $this->productMetadata = $productMetadata;
        $this->rowParser = $rowParser;
        $this->dataHashGenerator = $dataHashGenerator;
    }

    /**
     * Around get errors
     * 
     * @param \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\Import $subject
     * @param \Closure $proceed
     * @return array
     */
    public function aroundGetErrors(
        \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\Import $subject,
        \Closure $proceed
    )
    {
        return $this->errors;
    }

    /**
     * Around get data
     * 
     * @param \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\Import $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Filesystem\File\ReadInterface $file
     * @param int $websiteId
     * @param string $conditionShortName
     * @param string $conditionFullName
     * @param int $bunchSize
     * @return \Generator
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundGetData(
        \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\Import $subject,
        \Closure $proceed,
        \Magento\Framework\Filesystem\File\ReadInterface $file,
        $websiteId,
        $conditionShortName,
        $conditionFullName,
        $bunchSize = 5000
    )
    {
        $this->errors = [];
        $columnResolver = $this->columnResolverFactory->create(['headers' => $this->getHeaders($file)]);
        $rowNumber = 1;
        $items = [];
        while (false !== ($csvLine = $file->readCsv())) {
            try {
                $rowNumber++;
                if (empty($csvLine)) {
                    continue;
                }
                if (version_compare($this->productMetadata->getVersion(), '2.3.1', '>=')) {
                    $rowsData = $this->getRowsData($csvLine, $rowNumber, $websiteId, $conditionShortName, $conditionFullName, $columnResolver);
                    foreach ($rowsData as $rowData) {
                        $items[] = $rowData;
                    }
                    if (count($rowsData) > 1) {
                        $bunchSize += count($rowsData) - 1;
                    }
                } else {
                    $rowData = $this->getRowData($csvLine, $rowNumber, $websiteId, $conditionShortName, $conditionFullName, $columnResolver);
                    if ($rowData === null) {
                        continue;
                    }
                    $items[] = $rowData;
                }
                if (count($items) === $bunchSize) {
                    yield $items;
                    $items = [];
                }
            } catch (\Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\RowException $exception) {
                $this->errors[] = $exception->getMessage();
            }
        }
        if (count($items)) {
            yield $items;
        }
    }
    
    /**
     * Get headers
     * 
     * @param \Magento\Framework\Filesystem\File\ReadInterface $file
     * @return array|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getHeaders(\Magento\Framework\Filesystem\File\ReadInterface $file)
    {
        $headers = $file->readCsv();
        if ($headers !== false && count($headers) >= 6) {
            return $headers;
        }
        throw new \Magento\Framework\Exception\LocalizedException(__('The table rates file format is incorrect. Verify the format and try again.'));
    }

    /**
     * Add row data
     * 
     * @param int $rowNumber
     * @param array $rowData
     * @return void
     * @throws \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\RowException
     */
    private function addRowData($rowNumber, $rowData): void
    {
        $hash = $this->dataHashGenerator->getHash($rowData);
        if (array_key_exists($hash, $this->uniqueHash)) {
            throw new \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\RowException(
                __('Duplicate row #%1 (duplicates row #%2)', $rowNumber, $this->uniqueHash[$hash])
            );
        }
        $this->uniqueHash[$hash] = $rowNumber;
    }
    
    /**
     * Get rows data
     * 
     * @param string $csvLine
     * @param int $rowNumber
     * @param int $websiteId
     * @param string $conditionShortName
     * @param string $conditionFullName
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\OfflineShipping\ResourceModel\Carrier\Tablerate\CSV\ColumnResolver $columnResolver
     * @return array
     * @throws \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\RowException
     */
    private function getRowsData($csvLine, $rowNumber, $websiteId, $conditionShortName, $conditionFullName, $columnResolver): array
    {
        $rowsData = $this->rowParser->parse($csvLine, $rowNumber, $websiteId, $conditionShortName, $conditionFullName, $columnResolver);
        foreach ($rowsData as $rowData) {
            $this->addRowData($rowNumber, $rowData);
        }
        return $rowsData;
    }
    
    /**
     * Get row data
     * 
     * @param string $csvLine
     * @param int $rowNumber
     * @param int $websiteId
     * @param string $conditionShortName
     * @param string $conditionFullName
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\OfflineShipping\ResourceModel\Carrier\Tablerate\CSV\ColumnResolver $columnResolver
     * @return array
     * @throws \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CSV\RowException
     */
    private function getRowData($csvLine, $rowNumber, $websiteId, $conditionShortName, $conditionFullName, $columnResolver): array
    {
        $rowData = $this->rowParser->parse($csvLine, $rowNumber, $websiteId, $conditionShortName, $conditionFullName, $columnResolver);
        $this->addRowData($rowNumber, $rowData);
        return $rowData;
    }
}