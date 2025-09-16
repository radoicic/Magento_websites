<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote;

/**
 * Quote item wrapper
 */
class Item extends \Ecombricks\Common\DataObject\Wrapper
{
    /**
     * Wrapper factory
     * 
     * @var \Ecombricks\Common\DataObject\WrapperFactory
     */
    private $wrapperFactory;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory,
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
    )
    {
        parent::__construct($objectReflectionFactory);
        $this->wrapperFactory = $wrapperFactory;
    }

    /**
     * Get resource source wrapper
     * 
     * @return \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\ResourceModel\Quote\Item\Source
     */
    private function getResourceSourceWrapper(): \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\ResourceModel\Quote\Item\Source
    {
        return $this->wrapperFactory->create(
            $this->getObject()->getResource(),
            \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\ResourceModel\Quote\Item\Source::class
        );
    }
    
    /**
     * Get source wrapper
     * 
     * @return \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\Item\Source
     */
    private function getSourceWrapper(): \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\Item\Source
    {
        return $this->wrapperFactory->create(
            $this->getObject(),
            \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\Item\Source::class
        );
    }

    /**
     * After load
     * 
     * @return void
     */
    public function afterLoad(): void
    {
        $this->setSourceCode($this->getResourceSourceWrapper()->getSourceCode($this->getObject()->getId()));
    }

    /**
     * After save
     * 
     * @return void
     */
    public function afterSave(): void
    {
        $this->getResourceSourceWrapper()->saveSourceCode($this->getObject()->getId(), $this->getSourceCode());
    }
    
    /**
     * Before delete
     * 
     * @return void
     */
    public function beforeDelete(): void
    {
        $this->getResourceSourceWrapper()->deleteSourceCode($this->getObject()->getId());
    }
    
    /**
     * Set source code
     * 
     * @param string|null $sourceCode
     * @return void
     */
    public function setSourceCode(string $sourceCode = null): void
    {
        $this->getSourceWrapper()->setSourceCode($sourceCode);
    }
    
    /**
     * Get source code
     * 
     * @return string|null
     */
    public function getSourceCode(): ?string
    {
        return $this->getSourceWrapper()->getSourceCode();
    }
}