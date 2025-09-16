<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Catalog\Product\SourceItem\Option;

/**
 * Product source item price option plugin
 */
class Price extends \Ecombricks\InventoryCommon\Plugin\Model\Catalog\Product\SourceItem\Option
{
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Config $optionConfig
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Get $getOptions
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Meta $optionMeta
     * @param \Ecombricks\InventoryCommon\Model\GetCurrentSources $getCurrentSources
     * @param \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Config $optionConfig,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Get $getOptions,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Meta $optionMeta,
        \Ecombricks\InventoryCommon\Model\GetCurrentSources $getCurrentSources,
        \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        parent::__construct(
            $optionConfig,
            $getOptions,
            $optionMeta,
            $getCurrentSources,
            $isSourceItemManagementAllowedForProductType,
            $storeManager
        );
    }
}