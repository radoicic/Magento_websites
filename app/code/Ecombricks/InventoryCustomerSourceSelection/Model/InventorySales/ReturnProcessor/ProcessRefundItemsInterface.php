<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\ReturnProcessor;

/**
 * Process refund items Interface
 */
interface ProcessRefundItemsInterface
{
    /**
     * Execute
     * 
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\ReturnProcessor\Request\ItemsToRefundInterface[] $itemsToRefund
     * @param array $returnToStockItems
     * @return void
     */
    public function execute(
        \Magento\Sales\Api\Data\OrderInterface $order,
        array $itemsToRefund,
        array $returnToStockItems
    );
}