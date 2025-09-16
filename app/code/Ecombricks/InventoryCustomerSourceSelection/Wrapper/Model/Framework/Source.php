<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Framework;

/**
 * Source wrapper
 */
class Source extends \Ecombricks\Common\DataObject\Wrapper
{
    /**
     * Default source provider
     * 
     * @var \Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface
     */
    private $defaultSourceProvider;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory
     * @param \Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface $defaultSourceProvider
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory,
        \Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface $defaultSourceProvider
    )
    {
        parent::__construct($objectReflectionFactory);
        $this->defaultSourceProvider = $defaultSourceProvider;
    }
    
    /**
     * Set source code
     * 
     * @param string|null $sourceCode
     * @return void
     */
    public function setSourceCode(string $sourceCode = null): void
    {
        $this->getObject()->getExtensionAttributes()->setSourceCode($sourceCode);
    }
    
    /**
     * Get source code
     * 
     * @return string|null
     */
    public function getSourceCode(): ?string
    {
        $sourceCode = $this->getObject()->getExtensionAttributes()->getSourceCode();
        if ($sourceCode) {
            return $sourceCode;
        } else {
            return $this->defaultSourceProvider->getCode();
        }
    }
}