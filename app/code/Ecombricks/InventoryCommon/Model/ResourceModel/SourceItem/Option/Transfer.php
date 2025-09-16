<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model\ResourceModel\SourceItem\Option;

/**
 * Transfer source item options options resource
 */
class Transfer
{
    /**
     * Connection provider
     * 
     * @var \Ecombricks\Common\Model\ResourceModel\ConnectionProvider
     */
    private $connectionProvider;

    /**
     * Get product types by SKUs
     * 
     * @var \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface
     */
    private $getProductTypesBySkus;

    /**
     * Is source item management allowed for product type
     * 
     * @var \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface
     */
    private $isSourceItemManagementAllowedForProductType;

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
     * @param \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface $getProductTypesBySkus
     * @param \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param string $tableName
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider,
        \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface $getProductTypesBySkus,
        \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        string $tableName
    )
    {
        $this->connectionProvider = $connectionProvider;
        $this->getProductTypesBySkus = $getProductTypesBySkus;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->tableName = $tableName;
    }

    /**
     * Execute
     * 
     * @param array $skus
     * @param string $originSource
     * @param string $destinationSource
     * @return $this
     */
    public function execute(
        array $skus,
        string $originSource,
        string $destinationSource
    )
    {
        $filteredSkus = $this->filterSkus($skus);
        if (empty($filteredSkus)) {
            return $this;
        }
        $destinationOptionValues = $this->getOptionValues($filteredSkus, $destinationSource);
        $skusToInsert = array_diff($filteredSkus, array_keys($destinationOptionValues));
        if (empty($skusToInsert)) {
            return $this;
        }
        $originOptionValues = $this->getOptionValues($skusToInsert, $originSource);
        $optionsData = [];
        foreach ($originOptionValues as $sku => $value) {
            $optionsData[] = $this->getSourceItemOptionData($destinationSource, $sku, $value);
        }
        foreach (array_diff($skusToInsert, array_keys($originOptionValues)) as $sku) {
            $optionsData[] = $this->getSourceItemOptionData($destinationSource, $sku);
        }
        $this->connectionProvider->getConnection()->insertMultiple(
            $this->connectionProvider->getTable($this->tableName),
            $optionsData
        );
        return $this;
    }

    /**
     * Filter SKUs
     * 
     * @param array $skus
     * @return array
     */
    private function filterSkus(array $skus): array
    {
        $productTypes = $this->getProductTypesBySkus->execute($skus);
        foreach ($productTypes as $sku => $productType) {
            if (!$this->isSourceItemManagementAllowedForProductType->execute($productType)) {
                unset($productTypes[$sku]);
            }
        }
        return array_keys($productTypes);
    }

    /**
     * Get option values
     * 
     * @param array $skus
     * @param string $sourceCode
     * @return array
     */
    private function getOptionValues(array $skus, string $sourceCode): array
    {
        return $this->connectionProvider->getConnection()->fetchPairs(
            $this->connectionProvider->getSelect()
                ->from($this->connectionProvider->getTable($this->tableName), [
                    \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::SKU,
                    \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::VALUE,
                ])
                ->where(\Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::SKU.' IN (?)', $skus)
                ->where(\Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::SOURCE_CODE.' = ?', $sourceCode)
        );
    }

    /**
     * Get source item option data
     * 
     * @param string $sourceCode
     * @param string $sku
     * @param mixed $value
     * @return array
     */
    private function getSourceItemOptionData(string $sourceCode, string $sku, $value = null): array
    {
        return [
            \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::SOURCE_CODE => $sourceCode,
            \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::SKU => $sku,
            \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface::VALUE => $value,
        ];
    }
}