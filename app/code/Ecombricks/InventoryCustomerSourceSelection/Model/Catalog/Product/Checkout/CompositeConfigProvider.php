<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product\Checkout;

/**
 * Composite product checkout configuration provider
 */
class CompositeConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /**
     * Configuration providers
     * 
     * @var \Magento\Checkout\Model\ConfigProviderInterface[]
     */
    private $configProviders;
    
    /**
     * Constructor
     * 
     * @param \Magento\Checkout\Model\ConfigProviderInterface[] $configProviders
     * @return void
     */
    public function __construct(array $configProviders)
    {
        $this->configProviders = $configProviders;
    }

    /**
     * Get configuration
     * 
     * @return array
     */
    public function getConfig()
    {
        $config = [];
        foreach ($this->configProviders as $configProvider) {
            $config = array_merge_recursive($config, $configProvider->getConfig());
        }
        return $config;
    }
}