<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model\SourceItem\Option;

/**
 * Source item option meta
 */
class Meta
{
    /**
     * Name
     * 
     * @var string
     */
    private $name;

    /**
     * Label
     * 
     * @var \Magento\Framework\Phrase
     */
    private $label;

    /**
     * Table name
     * 
     * @var string
     */
    private $tableName;
    
    /**
     * Back-end type
     * 
     * @var string
     */
    private $backendType;
    
    /**
     * Front-end input
     * 
     * @var string
     */
    private $frontendInput;
    
    /**
     * Source model
     * 
     * @var string
     */
    private $sourceModel;

    /**
     * Constructor
     * 
     * @param string $name
     * @param string $label
     * @param string $tableName
     * @param string|null $frontendInput
     * @param string|null $sourceModel
     * @return void
     */
    public function __construct(
        string $name,
        string $label,
        string $tableName,
        string $backendType,
        string $frontendInput = null,
        string $sourceModel = null
    )
    {
        $this->name = $name;
        $this->label = __($label);
        $this->tableName = $tableName;
        $this->backendType = $backendType;
        $this->frontendInput = $frontendInput;
        $this->sourceModel = $sourceModel;
    }
    
    /**
     * Get name
     * 
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Get label
     * 
     * @return string
     */
    public function getLabel(): \Magento\Framework\Phrase
    {
        return $this->label;
    }
    
    /**
     * Get table name
     * 
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }
    
    /**
     * Get back-end type
     * 
     * @return string
     */
    public function getBackendType(): string
    {
        return $this->backendType;
    }
    
    /**
     * Get front-end input
     * 
     * @return string|null
     */
    public function getFrontendInput(): ?string
    {
        return $this->frontendInput;
    }

    /**
     * Get source model
     * 
     * @return string|null
     */
    public function getSourceModel(): ?string
    {
        return $this->sourceModel;
    }
}