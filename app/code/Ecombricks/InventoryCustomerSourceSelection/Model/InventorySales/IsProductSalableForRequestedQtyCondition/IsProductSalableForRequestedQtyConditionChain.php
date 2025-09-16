<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\IsProductSalableForRequestedQtyCondition;

/**
 * Is salable for requested qty condition chain
 */
class IsProductSalableForRequestedQtyConditionChain extends \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\IsProductSalableForRequestedQtyCondition\AbstractCondition
{
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
     * Conditions
     * 
     * @var \Magento\InventorySalesApi\Api\IsProductSalableForRequestedQtyInterface[]
     */
    private $conditions;

    /**
     * Not required conditions
     * 
     * @var \Magento\InventorySalesApi\Api\IsProductSalableForRequestedQtyInterface[]
     */
    private $unrequiredConditions;

    /**
     * Required conditions
     * 
     * @var \Magento\InventorySalesApi\Api\IsProductSalableForRequestedQtyInterface[]
     */
    private $requiredConditions;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Model\GetStockIdBySourceCode $getStockIdBySourceCode
     * @param \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface $getProductTypesBySkus
     * @param \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param \Magento\InventorySalesApi\Api\Data\ProductSalabilityErrorInterfaceFactory $productSalabilityErrorFactory
     * @param \Magento\InventorySalesApi\Api\Data\ProductSalableResultInterfaceFactory $productSalableResultFactory
     * @param array $conditions
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Model\GetStockIdBySourceCode $getStockIdBySourceCode,
        \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface $getProductTypesBySkus,
        \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        \Magento\InventorySalesApi\Api\Data\ProductSalabilityErrorInterfaceFactory $productSalabilityErrorFactory,
        \Magento\InventorySalesApi\Api\Data\ProductSalableResultInterfaceFactory $productSalableResultFactory,
        array $conditions
    )
    {
        parent::__construct(
            $getStockIdBySourceCode,
            $productSalabilityErrorFactory,
            $productSalableResultFactory
        );
        $this->getProductTypesBySkus = $getProductTypesBySkus;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->conditions = $conditions;
    }

    /**
     * Execute
     * 
     * @param string $sku
     * @param string $sourceCode
     * @param float $requestedQty
     * @return \Magento\InventorySalesApi\Api\Data\ProductSalableResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(string $sku, string $sourceCode, float $requestedQty): \Magento\InventorySalesApi\Api\Data\ProductSalableResultInterface
    {
        $this->validateProductType($sku);
        if (!empty($this->conditions) && empty($this->unrequiredConditions) && empty($this->requiredConditions)) {
            $this->setConditions();
        }
        try {
            $requiredConditionsErrors = $this->processRequiredConditions($sku, $sourceCode, $requestedQty);
            if (count($requiredConditionsErrors)) {
                return $this->createProductSalableResult($requiredConditionsErrors);
            }
            $sufficientConditionsErrors = $this->processNotrequiredConditions($sku, $sourceCode, $requestedQty);
            if (count($sufficientConditionsErrors)) {
                return $this->createProductSalableResult($sufficientConditionsErrors);
            }
        } catch (\Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException $exception) {
            return $this->createProductSalableResult([$this->createProductSalabilityError(
                'requested-sku-is-not-assigned-to-given-stock', 
                __('The requested sku is not assigned to given stock.')
            )]);
        }
        return $this->createProductSalableResult([]);
    }

    /**
     * Validate product type
     * 
     * @param string $sku
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function validateProductType(string $sku): bool
    {
        $productType = $this->getProductTypesBySkus->execute([$sku])[$sku];
        if ($this->isSourceItemManagementAllowedForProductType->execute($productType) !== false) {
            return true;
        }
        throw new \Magento\Framework\Exception\LocalizedException(
            __('Can\'t check requested quantity for products without source items support.')
        );
    }
    
    /**
     * Validate conditions
     * 
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function validateConditions(): bool
    {
        foreach ($this->conditions as $condition) {
            if (empty($condition['object'])) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Parameter "object" must be present.'));
            }
            if (empty($condition['required']) && empty($condition['sort_order'])) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Parameter "sort_order" must be present for urequired conditions.'));
            }
            if (
                !$condition['object'] instanceof \Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales\IsProductSalableForRequestedQtyInterface && 
                !$condition['object'] instanceof \Magento\InventorySalesApi\Api\IsProductSalableForRequestedQtyInterface
            ) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Incorrect condition.'));
            }
        }
        return true;
    }
    
    /**
     * Sort conditions
     * 
     * @param array $conditions
     * @return array
     */
    private function sortConditions(array $conditions): array
    {
        usort($conditions, function (array $conditionLeft, array $conditionRight) {
            if ($conditionLeft['sort_order'] == $conditionRight['sort_order']) {
                return 0;
            }
            return ($conditionLeft['sort_order'] < $conditionRight['sort_order']) ? -1 : 1;
        });
        return $conditions;
    }

    /**
     * Set conditions
     * 
     * @return void
     */
    private function setConditions(): void
    {
        $this->validateConditions();
        $unrequiredConditions = array_filter(
            $this->conditions,
            function ($item) { return !isset($item['required']); }
        );
        $this->unrequiredConditions = array_column($this->sortConditions($unrequiredConditions), 'object');
        $requiredConditions = array_filter(
            $this->conditions,
            function ($item) { return isset($item['required']) && (bool) $item['required']; }
        );
        $this->requiredConditions = array_column($requiredConditions, 'object');
    }

    /**
     * Execute condition
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales\IsProductSalableForRequestedQtyInterface|\Magento\InventorySalesApi\Api\IsProductSalableForRequestedQtyInterface $condition
     * @param string $sku
     * @param string $sourceCode
     * @param float $requestedQty
     * @return \Magento\InventorySalesApi\Api\Data\ProductSalableResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function executeCondition(
        $condition,
        string $sku,
        string $sourceCode,
        float $requestedQty
    ): \Magento\InventorySalesApi\Api\Data\ProductSalableResultInterface
    {
        if ($condition instanceof \Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales\IsProductSalableForRequestedQtyInterface) {
            return $condition->execute($sku, $sourceCode, $requestedQty);
        } else {
            $stockId = $this->getStockIdBySourceCode->execute($sourceCode);
            return $condition->execute($sku, $stockId, $requestedQty);
        }
    }
    
    /**
     * Process required conditions
     *
     * @param string $sku
     * @param string $sourceCode
     * @param float $requestedQty
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function processRequiredConditions(string $sku, string $sourceCode, float $requestedQty): array
    {
        $errors = [[]];
        foreach ($this->requiredConditions as $condition) {
            $productSalableResult = $this->executeCondition($condition, $sku, $sourceCode, $requestedQty);
            if ($productSalableResult->isSalable()) {
                continue;
            }
            $errors[] = $productSalableResult->getErrors();
        }
        return array_merge(...$errors);
    }

    /**
     * Process not required conditions
     *
     * @param string $sku
     * @param string $sourceCode
     * @param float $requestedQty
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function processNotrequiredConditions(string $sku, string $sourceCode, float $requestedQty): array
    {
        $errors = [[]];
        foreach ($this->unrequiredConditions as $condition) {
            $productSalableResult = $this->executeCondition($condition, $sku, $sourceCode, $requestedQty);
            if ($productSalableResult->isSalable()) {
                return [];
            }
            $errors[] = $productSalableResult->getErrors();
        }
        return array_merge(...$errors);
    }
}