<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventoryImportExport\Import\Validator;

/**
 * Price validator
 */
class PriceValidator implements \Magento\InventoryImportExport\Model\Import\Validator\ValidatorInterface
{
    /**
     * Validation result factory
     * 
     * @var \Magento\Framework\Validation\ValidationResultFactory
     */
    private $validationResultFactory;
    
    /**
     * Constructor
     * 
     * @param \Magento\Framework\Validation\ValidationResultFactory $validationResultFactory
     */
    public function __construct(
        \Magento\Framework\Validation\ValidationResultFactory $validationResultFactory
    )
    {
        $this->validationResultFactory = $validationResultFactory;
    }
    
    /**
     * @inheritdoc
     */
    public function validate(array $rowData, int $rowNumber)
    {
        $errors = [];
        if (isset($rowData['price']) && $rowData['price'] && !is_numeric($rowData['price'])) {
            $errors[] = __('Invalid price');
        }
        return $this->validationResultFactory->create([ 'errors' => $errors, ]);
    }
}