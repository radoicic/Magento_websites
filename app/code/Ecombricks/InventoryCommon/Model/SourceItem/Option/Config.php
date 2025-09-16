<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model\SourceItem\Option;

/**
 * Source item option configuration
 */
class Config extends \Ecombricks\Common\Model\Config
{
    /**
     * Path
     * 
     * @var string
     */
    private $path;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param string $path
     * @return void
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        string $path
    )
    {
        parent::__construct($scopeConfig);
        $this->path = $path;
    }
    
    /**
     * Check if is enabled
     * 
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isSetFlag($this->path);
    }
}