<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Ecombricks\Common\Model;

/**
 * Configuration
 */
class Config
{
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
     * Check if flag is set
     *
     * @return bool
     */
    public function isSetFlag($path)
    {
        return $this->scopeConfig->isSetFlag($path, \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }
}