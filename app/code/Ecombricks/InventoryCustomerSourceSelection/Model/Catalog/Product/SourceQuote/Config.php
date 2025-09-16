<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product\SourceQuote;

/**
 * Product source quote configuration
 */
class Config extends \Ecombricks\Common\Model\Config
{
    /**
     * Paths
     */
    const XML_PATH_CATALOG_PRODUCT_SOURCE_QUOTE_ACTIVE = 'catalog/product_source_quote/active';
    const XML_PATH_CATALOG_PRODUCT_SOURCE_QUOTE_CURRENT_SOURCE_ONLY = 'catalog/product_source_quote/current_source_only';

    /**
     * Check if is enabled
     * 
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->isSetFlag(self::XML_PATH_CATALOG_PRODUCT_SOURCE_QUOTE_ACTIVE);
    }
    
    /**
     * Check if is current source only
     * 
     * @return bool
     */
    public function isCurrentSourceOnly()
    {
        return (bool) $this->isSetFlag(self::XML_PATH_CATALOG_PRODUCT_SOURCE_QUOTE_CURRENT_SOURCE_ONLY);
    }
}