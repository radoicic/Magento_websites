<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Store;

/**
 * Store plugin
 */
class Store
{
    /**
     * Configuration cache
     * 
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    private $configCache;

    /**
     * Resource
     * 
     * @var \Magento\Store\Model\ResourceModel\Store
     */
    private $resource;

    /**
     * Constructor
     * 
     * @param \Magento\Framework\App\Cache\Type\Config $configCache
     * @param \Magento\Store\Model\ResourceModel\Store $resource
     * @return void
     */
    public function __construct(
        \Magento\Framework\App\Cache\Type\Config $configCache,
        \Magento\Store\Model\ResourceModel\Store $resource
    )
    {
        $this->configCache = $configCache;
        $this->resource = $resource;
    }

    /**
     * After after delete
     * 
     * @param \Magento\Store\Model\Store $subject
     * @param \Magento\Store\Model\Store $result
     * @return \Magento\Store\Model\Store
     */
    public function afterAfterDelete(
        \Magento\Store\Model\Store $subject,
        $result
    )
    {
        $this->resource->getConnection()->delete(
            $this->resource->getTable('ecombricks_inventory__source_core_config_data'),
            [
                'scope = ?' => \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
                'scope_id = ?' => $subject->getStoreId(),
            ]
        );
        $this->configCache->clean();
        return $result;
    }
}