<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventorySales;

/**
 * Get items to cancel from order item plugin
 */
class GetItemsToCancelFromOrderItem
{
    /**
     * Order item wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory 
     */
    private $orderItemWrapperFactory;

    /**
     * JSON serializer
     * 
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonSerializer;

    /**
     * Items to sell factory
     * 
     * @var \Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory
     */
    private $itemsToSellFactory;

    /**
     * Get SKU from order item
     * 
     * @var \Magento\InventorySalesApi\Model\GetSkuFromOrderItemInterface
     */
    private $getSkuFromOrderItem;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory $orderItemWrapperFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     * @param \Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory $itemsToSellFactory
     * @param \Magento\InventorySalesApi\Model\GetSkuFromOrderItemInterface $getSkuFromOrderItem
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory $orderItemWrapperFactory,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        \Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory $itemsToSellFactory,
        \Magento\InventorySalesApi\Model\GetSkuFromOrderItemInterface $getSkuFromOrderItem
    )
    {
        $this->orderItemWrapperFactory = $orderItemWrapperFactory;
        $this->jsonSerializer = $jsonSerializer;
        $this->itemsToSellFactory = $itemsToSellFactory;
        $this->getSkuFromOrderItem = $getSkuFromOrderItem;
    }

    /**
     * Around execute
     * 
     * @param \Magento\InventorySales\Model\GetItemsToCancelFromOrderItem $subject
     * @param callable $proceed
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @return \Magento\InventorySalesApi\Api\Data\ItemToSellInterface[]
     */
    public function aroundExecute(
        \Magento\InventorySales\Model\GetItemsToCancelFromOrderItem $subject,
        callable $proceed,
        \Magento\Sales\Model\Order\Item $orderItem
    ): array
    {
        $itemsToCancel = [];
        if ($orderItem->isDummy(true)) {
            return $itemsToCancel;
        }
        if ($orderItem->getHasChildren()) {
            foreach ($this->processComplexItem($orderItem) as $item) {
                $itemsToCancel[] = $item;
            }
        } else {
            $sku = $this->getSkuFromOrderItem->execute($orderItem);
            $qty = $this->getQtyToCancel($orderItem);
            $sourceCode = $this->orderItemWrapperFactory->create($orderItem)->getSourceCode();
            $item = $this->itemsToSellFactory->create(['sku' => $sku, 'qty' => $qty]);
            $item->getExtensionAttributes()->setSourceCode($sourceCode);
            $itemsToCancel[] = $item;
        }
        return $this->groupItemsBySkuAndSourceCode($itemsToCancel);
    }
    
    /**
     * Get qty to cancel
     * 
     * @param \Magento\Sales\Model\Order\Item $item
     * @return float
     */
    private function getQtyToCancel(\Magento\Sales\Model\Order\Item $item): float
    {
        return $item->getQtyOrdered() - max($item->getQtyShipped(), $item->getQtyInvoiced()) - $item->getQtyCanceled();
    }
    
    /**
     * Process complex item
     * 
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @return \Magento\InventorySalesApi\Api\Data\ItemToSellInterface[]
     */
    private function processComplexItem(\Magento\Sales\Model\Order\Item $orderItem): array
    {
        $itemsToCancel = [];
        foreach ($orderItem->getChildrenItems() as $childOrderItem) {
            $sourceCode = $this->orderItemWrapperFactory->create($childOrderItem)->getSourceCode();
            $productOptions = $childOrderItem->getProductOptions();
            if (isset($productOptions['bundle_selection_attributes'])) {
                $bundleSelectionAttributes = $this->jsonSerializer->unserialize($productOptions['bundle_selection_attributes']);
                if (empty($bundleSelectionAttributes)) {
                    continue;
                }
                $sku = $this->getSkuFromOrderItem->execute($childOrderItem);
                $shippedQty = $bundleSelectionAttributes['qty'] * $orderItem->getQtyShipped();
                $qty = $childOrderItem->getQtyOrdered() - max($shippedQty, $childOrderItem->getQtyInvoiced()) - $childOrderItem->getQtyCanceled();
            } else {
                $sku = $this->getSkuFromOrderItem->execute($orderItem);
                $qty = $this->getQtyToCancel($orderItem);
            }
            $item = $this->itemsToSellFactory->create(['sku' => $sku, 'qty' => $qty]);
            $item->getExtensionAttributes()->setSourceCode($sourceCode);
            $itemsToCancel[] = $item;
        }
        return $itemsToCancel;
    }
    
    /**
     * Group items by SKU and source code
     * 
     * @param \Magento\InventorySalesApi\Api\Data\ItemToSellInterface[] $itemsToCancel
     * @return \Magento\InventorySalesApi\Api\Data\ItemToSellInterface[]
     */
    private function groupItemsBySkuAndSourceCode(array $itemsToCancel): array
    {
        $processingItems = $groupedItems = [];
        foreach ($itemsToCancel as $item) {
            $quantity = $item->getQuantity();
            if ($quantity == 0) {
                continue;
            }
            $sku = $item->getSku();
            $sourceCode = $item->getExtensionAttributes()->getSourceCode();
            if (empty($processingItems[$sku][$sourceCode])) {
                $processingItems[$sku][$sourceCode] = $quantity;
            } else {
                $processingItems[$sku][$sourceCode] += $quantity;
            }
        }
        foreach ($processingItems as $sku => $sourceQtys) {
            foreach ($sourceQtys as $sourceCode => $qty) {
                $item = $this->itemsToSellFactory->create([
                    'sku' => $sku,
                    'qty' => $qty,
                ]);
                $item->getExtensionAttributes()->setSourceCode((string) $sourceCode);
                $groupedItems[] = $item;
            }
        }
        return $groupedItems;
    }
}