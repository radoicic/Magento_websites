<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\SalesInventory\Order;

/**
 * Order return processor plugin
 */
class ReturnProcessor
{
    /**
     * Process refund items
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\ReturnProcessor\ProcessRefundItemsInterface
     */
    private $processRefundItems;

    /**
     * Items to refund factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\ReturnProcessor\Request\ItemsToRefundInterfaceFactory
     */
    private $itemsToRefundFactory;

    /**
     * Order item wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory 
     */
    private $orderItemWrapperFactory;

    /**
     * Get product types by SKUs
     * 
     * @var \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface 
     */
    private $getProductTypesBySkus;

    /**
     * Is source item management allowed for product type
     * 
     * @var \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface 
     */
    private $isSourceItemManagementAllowedForProductType;

    /**
     * Get SKU from order item
     * 
     * @var \Magento\InventorySalesApi\Model\GetSkuFromOrderItemInterface
     */
    private $getSkuFromOrderItem;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\ReturnProcessor\ProcessRefundItemsInterface $processRefundItems
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\ReturnProcessor\Request\ItemsToRefundInterfaceFactory $itemsToRefundFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory $orderItemWrapperFactory
     * @param \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface $getProductTypesBySkus
     * @param \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param \Magento\InventorySalesApi\Model\GetSkuFromOrderItemInterface $getSkuFromOrderItem
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\ReturnProcessor\ProcessRefundItemsInterface $processRefundItems,
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\ReturnProcessor\Request\ItemsToRefundInterfaceFactory $itemsToRefundFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory $orderItemWrapperFactory,
        \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface $getProductTypesBySkus,
        \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        \Magento\InventorySalesApi\Model\GetSkuFromOrderItemInterface $getSkuFromOrderItem
    )
    {
        $this->processRefundItems = $processRefundItems;
        $this->itemsToRefundFactory = $itemsToRefundFactory;
        $this->orderItemWrapperFactory = $orderItemWrapperFactory;
        $this->getProductTypesBySkus = $getProductTypesBySkus;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->getSkuFromOrderItem = $getSkuFromOrderItem;
    }

    /**
     * Around execute
     * 
     * @param \Magento\SalesInventory\Model\Order\ReturnProcessor $subject
     * @param callable $proceed
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param array $returnToStockItems
     * @param bool $isAutoReturn
     * @return void
     */
    public function aroundExecute(
        \Magento\SalesInventory\Model\Order\ReturnProcessor $subject,
        callable $proceed,
        \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo,
        \Magento\Sales\Api\Data\OrderInterface $order,
        array $returnToStockItems = [],
        $isAutoReturn = false
    )
    {
        $items = [];
        foreach ($creditmemo->getItems() as $item) {
            if ($isAutoReturn || in_array($item->getOrderItemId(), $returnToStockItems)) {
                $orderItem = $item->getOrderItem();
                $sku = $this->getSkuFromOrderItem->execute($orderItem);
                if (!$this->isValidItem($sku, $orderItem->getProductType())) {
                    continue;
                }
                $sourceCode = (string) $this->orderItemWrapperFactory->create($orderItem)->getSourceCode();
                $qty = (float) $item->getQty();
                $processedQty = (float) ($orderItem->getQtyInvoiced() - $orderItem->getQtyRefunded() + $qty);
                $items[$sku][$sourceCode] = [
                    'qty' => ($items[$sku][$sourceCode]['qty'] ?? 0) + $qty,
                    'processedQty' => ($items[$sku][$sourceCode]['processedQty'] ?? 0) + $processedQty,
                ];
            }
        }
        $itemsToRefund = [];
        foreach ($items as $sku => $sources) {
            foreach ($sources as $sourceCode => $source) {
                $itemsToRefund[] = $this->itemsToRefundFactory->create([
                    'sku' => $sku,
                    'sourceCode' => (string) $sourceCode,
                    'qty' => $source['qty'],
                    'processedQty' => $source['processedQty'],
                ]);
            }
        }
        $this->processRefundItems->execute($order, $itemsToRefund, $returnToStockItems);
    }

    /**
     * Is valid item
     * 
     * @param string $sku
     * @param string|null $typeId
     * @return bool
     */
    private function isValidItem(string $sku, ?string $typeId): bool
    {
        if ($typeId === 'grouped') {
            $typeId = 'simple';
        }
        $productType = $typeId ?: $this->getProductTypesBySkus->execute([$sku])[$sku];
        return $this->isSourceItemManagementAllowedForProductType->execute($productType);
    }
}