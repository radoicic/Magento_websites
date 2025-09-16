<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Config\Config;

/**
 * Configuration schema locator plugin
 */
class SchemaLocator
{
    /**
     * URN resolver
     * 
     * @var \Magento\Framework\Config\Dom\UrnResolver 
     */
    private $urnResolver;

    /**
     * Constructor
     * 
     * @param \Magento\Framework\Config\Dom\UrnResolver $urnResolver
     * @return void
     */
    public function __construct(\Magento\Framework\Config\Dom\UrnResolver $urnResolver)
    {
        $this->urnResolver = $urnResolver;
    }

    /**
     * Around get schema
     * 
     * @param \Magento\Config\Model\Config\SchemaLocator $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundGetSchema(
        \Magento\Config\Model\Config\SchemaLocator $subject,
        \Closure $proceed
    )
    {
        return $this->urnResolver->getRealPath('urn:ecombricks:module:Ecombricks_InventoryCustomerSourceSelection:etc/inventory_system.xsd');
    }

    /**
     * Around get per file schema
     * 
     * @param \Magento\Config\Model\Config\SchemaLocator $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundGetPerFileSchema(
        \Magento\Config\Model\Config\SchemaLocator $subject,
        \Closure $proceed
    )
    {
        return $this->urnResolver->getRealPath('urn:ecombricks:module:Ecombricks_InventoryCustomerSourceSelection:etc/inventory_system_file.xsd');
    }
}