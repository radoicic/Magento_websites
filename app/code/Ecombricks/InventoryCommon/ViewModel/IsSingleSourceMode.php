<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\ViewModel;

/**
 * Is single source mode view model
 */
class IsSingleSourceMode implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * Is single source mode
     * 
     * @var \Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface
     */
    private $isSingleSourceMode;
    
    /**
     * Constructor
     * 
     * @param \Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface $isSingleSourceMode
     */
    public function __construct(
        \Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface $isSingleSourceMode
    )
    {
        $this->isSingleSourceMode = $isSingleSourceMode;
    }
    
    /**
     * Execute
     * 
     * @return bool
     */
    public function execute(): bool
    {
        return $this->isSingleSourceMode->execute();
    }
}