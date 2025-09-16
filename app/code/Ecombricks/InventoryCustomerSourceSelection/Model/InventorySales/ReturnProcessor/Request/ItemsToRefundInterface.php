<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\ReturnProcessor\Request;

/**
 * Items to refund return processor request interface
 */
interface ItemsToRefundInterface extends \Magento\InventorySalesApi\Model\ReturnProcessor\Request\ItemsToRefundInterface
{
    /**
     * Get source code
     * 
     * @return string
     */
    public function getSourceCode(): string;
}