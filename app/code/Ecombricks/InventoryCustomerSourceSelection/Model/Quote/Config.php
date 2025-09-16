<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Quote;

/**
 * Quote configuration
 */
class Config
{
    /**
     * Split order configuration path
     */
    const XML_PATH_SPLIT_ORDER = 'cataloginventory/source_options/split_order';

    /**
     * Scope configuration
     * 
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @return void
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check if is split order
     *
     * @return bool
     */
    public function isSplitOrder(): bool
    {
        return $this->isSetFlag(static::XML_PATH_SPLIT_ORDER);
    }
    
    /**
     * Check if flag is set
     *
     * @return bool
     */
    private function isSetFlag($path): bool
    {
        return (bool) $this->scopeConfig->isSetFlag($path, \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }
}