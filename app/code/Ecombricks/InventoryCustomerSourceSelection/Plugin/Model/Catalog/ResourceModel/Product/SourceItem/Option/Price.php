<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Catalog\ResourceModel\Product\SourceItem\Option;

/**
 * Product resource source item price options plugin
 */
class Price extends \Ecombricks\InventoryCommon\Plugin\Model\Catalog\ResourceModel\Product\SourceItem\Option
{
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Api\Inventory\SourceItem\Option\Price\GetInterface $getOptions
     * @param \Ecombricks\InventoryCustomerSourceSelection\Api\Inventory\SourceItem\Option\Price\SaveInterface $saveOptions
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Config $optionConfig
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Meta $optionMeta
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Processor $optionsProcessor
     * @param \Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface $defaultSourceProvider
     * @param \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param \Psr\Log\LoggerInterface $logger
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Api\Inventory\SourceItem\Option\Price\GetInterface $getOptions,
        \Ecombricks\InventoryCustomerSourceSelection\Api\Inventory\SourceItem\Option\Price\SaveInterface $saveOptions,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Config $optionConfig,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Meta $optionMeta,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\Price\Processor $optionsProcessor,
        \Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface $defaultSourceProvider,
        \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        \Psr\Log\LoggerInterface $logger
    )
    {
        parent::__construct(
            $getOptions,
            $saveOptions,
            $optionConfig,
            $optionMeta,
            $optionsProcessor,
            $defaultSourceProvider,
            $isSourceItemManagementAllowedForProductType,
            $logger
        );
    }
}