<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Framework;

/**
 * Source option wrapper
 */
class SourceOption extends \Ecombricks\Common\DataObject\Wrapper
{
    /**
     * Attribute code
     * 
     * @var string
     */
    private $attributeCode;

    /**
     * Source codes
     * 
     * @var array
     */
    private $sourceCodes = [];
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory
     * @param string $attributeCode
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory,
        string $attributeCode
    )
    {
        parent::__construct($objectReflectionFactory);
        $this->attributeCode = $attributeCode;
    }

    /**
     * Set source codes
     * 
     * @param array $sourceCodes
     * @return void
     */
    public function setSourceCodes(array $sourceCodes): void
    {
        $this->sourceCodes = $sourceCodes;
    }
    
    /**
     * Get source codes
     * 
     * @return array
     */
    public function getSourceCodes(): array
    {
        return $this->sourceCodes;
    }

    /**
     * Filter source options
     * 
     * @param array $sourceOptions
     * @return array
     */
    public function filterSourceOptions(array $sourceOptions)
    {
        if (empty($sourceOptions)) {
            return [];
        }
        $filteredSourceOptions = [];
        $sourceCodes = $this->getSourceCodes();
        if (empty($sourceCodes)) {
            return $sourceOptions;
        }
        foreach ($this->getSourceCodes() as $sourceCode) {
            if (empty($sourceOptions[$sourceCode])) {
                $filteredSourceOptions = [];
                break;
            }
            $filteredSourceOptions[$sourceCode] = $sourceOptions[$sourceCode];
        }
        return $filteredSourceOptions;
    }

    /**
     * Set source options
     * 
     * @param array $sourceOptions
     * @return void
     */
    public function setSourceOptions(array $sourceOptions): void
    {
        $method = 'set'.str_replace('_', '', ucwords($this->attributeCode, '_'));
        $this->getObject()->getExtensionAttributes()->{$method}($this->filterSourceOptions($sourceOptions));
    }

    /**
     * Get source options
     * 
     * @return array
     */
    public function getSourceOptions(): array
    {
        $method = 'get'.str_replace('_', '', ucwords($this->attributeCode, '_'));
        return $this->getObject()->getExtensionAttributes()->{$method}() ?? [];
    }

    /**
     * Set source option
     * 
     * @param string $sourceCode
     * @param mixed $value
     * @return void
     */
    public function setSourceOption(string $sourceCode, $value): void
    {
        $sourceOptions = $this->getSourceOptions();
        if (!$sourceOptions) {
            $sourceOptions = [];
        }
        $sourceOptions[$sourceCode] = $value;
        $this->setSourceOptions($sourceOptions);
    }

    /**
     * Get source option
     * 
     * @param string $sourceCode
     * @return mixed|null
     */
    public function getSourceOption(string $sourceCode)
    {
        $sourceOptions = $this->getSourceOptions();
        return $sourceOptions && !empty($sourceOptions[$sourceCode]) ? $sourceOptions[$sourceCode] : null;
    }
}