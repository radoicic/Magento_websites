<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Inventory\SourceItem\Option\TaxClass;

/**
 * Source item tax class option configuration
 */
class Config extends \Ecombricks\InventoryCommon\Model\SourceItem\Option\Config 
{
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param string $path
     * @return void
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        string $path = 'cataloginventory/source_options/enable_tax_class'
    )
    {
        parent::__construct($scopeConfig, $path);
    }
}