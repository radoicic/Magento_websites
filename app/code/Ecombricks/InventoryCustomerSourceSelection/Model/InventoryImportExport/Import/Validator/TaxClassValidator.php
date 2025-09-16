<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventoryImportExport\Import\Validator;

/**
 * Tax class validator
 */
class TaxClassValidator implements \Magento\InventoryImportExport\Model\Import\Validator\ValidatorInterface
{
    /**
     * Filter builder
     * 
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;
    
    /**
     * Search criteria builder
     * 
     * @var \Magento\Framework\Api\SearchCriteriaBuilder 
     */
    private $searchCriteriaBuilder;
    
    /**
     * Validation result factory
     * 
     * @var \Magento\Framework\Validation\ValidationResultFactory
     */
    private $validationResultFactory;
    
    /**
     * Tax class repository
     * 
     * @var \Magento\Tax\Api\TaxClassRepositoryInterface 
     */
    private $taxClassRepository;
    
    /**
     * Product tax class IDs
     * 
     * @var array
     */
    private $productTaxClassIds;
    
    /**
     * Constructor
     * 
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Validation\ValidationResultFactory $validationResultFactory
     * @param \Magento\Tax\Api\TaxClassRepositoryInterface $taxClassRepository
     */
    public function __construct(
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Validation\ValidationResultFactory $validationResultFactory,
        \Magento\Tax\Api\TaxClassRepositoryInterface $taxClassRepository
    )
    {
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->validationResultFactory = $validationResultFactory;
        $this->taxClassRepository = $taxClassRepository;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $rowData, int $rowNumber)
    {
        $errors = [];
        if (isset($rowData['tax_class']) && $rowData['tax_class']) {
            if (!in_array((int) $rowData['tax_class'], $this->getProductTaxClassIds())) {
                $errors[] = __('Invalid product tax class identifier');
            }
        }
        return $this->validationResultFactory->create([ 'errors' => $errors, ]);
    }
    
    /**
     * Get product tax class IDs
     * 
     * @return array
     */
    private function getProductTaxClassIds(): array
    {
        if ($this->productTaxClassIds !== null) {
            return $this->productTaxClassIds;
        }
        $this->productTaxClassIds = [];
        $this->filterBuilder->setField(\Magento\Tax\Model\ClassModel::KEY_TYPE);
        $this->filterBuilder->setValue(\Magento\Tax\Api\TaxClassManagementInterface::TYPE_PRODUCT);
        $this->searchCriteriaBuilder->addFilters([ $this->filterBuilder->create(), ]);
        $searchResults = $this->taxClassRepository->getList($this->searchCriteriaBuilder->create());
        foreach ($searchResults->getItems() as $taxClass) {
            $this->productTaxClassIds[] = (int) $taxClass->getClassId();
        }
        return $this->productTaxClassIds;
    }
}