<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\ReturnProcessor;

/**
 * Process refund items
 */
class ProcessRefundItems implements \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\ReturnProcessor\ProcessRefundItemsInterface
{
    /**
     * Sales channel factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales\Data\WebsiteSalesChannelInterfaceFactory
     */
    private $salesChannelFactory;

    /**
     * Sales event factory
     * 
     * @var \Magento\InventorySalesApi\Api\Data\SalesEventInterfaceFactory
     */
    private $salesEventFactory;

    /**
     * Sales event extension factory
     * 
     * @var \Magento\InventorySalesApi\Api\Data\SalesEventExtensionFactory 
     */
    private $salesEventExtensionFactory;

    /**
     * Items to sell factory
     * 
     * @var \Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory
     */
    private $itemsToSellFactory;

    /**
     * Place reservations for sales event
     * 
     * @var \Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface
     */
    private $placeReservationsForSalesEvent;

    /**
     * Get source deducted order items
     * 
     * @var \Magento\InventorySalesApi\Model\ReturnProcessor\GetSourceDeductedOrderItemsInterface
     */
    private $getSourceDeductedOrderItems;

    /**
     * Item to deduct factory
     * 
     * @var \Magento\InventorySourceDeductionApi\Model\ItemToDeductFactory
     */
    private $itemToDeductFactory;

    /**
     * Source deduction request factory
     * 
     * @var \Magento\InventorySourceDeductionApi\Model\SourceDeductionRequestFactory
     */
    private $sourceDeductionRequestFactory;

    /**
     * Source deduction service
     * 
     * @var \Magento\InventorySourceDeductionApi\Model\SourceDeductionService
     */
    private $sourceDeductionService;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales\Data\WebsiteSalesChannelInterfaceFactory $salesChannelFactory
     * @param \Magento\InventorySalesApi\Api\Data\SalesEventInterfaceFactory $salesEventFactory
     * @param \Magento\InventorySalesApi\Api\Data\SalesEventExtensionFactory $salesEventExtensionFactory
     * @param \Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory $itemsToSellFactory
     * @param \Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface $placeReservationsForSalesEvent
     * @param \Magento\InventorySalesApi\Model\ReturnProcessor\GetSourceDeductedOrderItemsInterface $getSourceDeductedOrderItems
     * @param \Magento\InventorySourceDeductionApi\Model\ItemToDeductFactory $itemToDeductFactory
     * @param \Magento\InventorySourceDeductionApi\Model\SourceDeductionRequestFactory $sourceDeductionRequestFactory
     * @param \Magento\InventorySourceDeductionApi\Model\SourceDeductionService $sourceDeductionService
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales\Data\WebsiteSalesChannelInterfaceFactory $salesChannelFactory,
        \Magento\InventorySalesApi\Api\Data\SalesEventInterfaceFactory $salesEventFactory,
        \Magento\InventorySalesApi\Api\Data\SalesEventExtensionFactory $salesEventExtensionFactory,
        \Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory $itemsToSellFactory,
        \Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface $placeReservationsForSalesEvent,
        \Magento\InventorySalesApi\Model\ReturnProcessor\GetSourceDeductedOrderItemsInterface $getSourceDeductedOrderItems,
        \Magento\InventorySourceDeductionApi\Model\ItemToDeductFactory $itemToDeductFactory,
        \Magento\InventorySourceDeductionApi\Model\SourceDeductionRequestFactory $sourceDeductionRequestFactory,
        \Magento\InventorySourceDeductionApi\Model\SourceDeductionService $sourceDeductionService
    )
    {
        $this->salesChannelFactory = $salesChannelFactory;
        $this->salesEventFactory = $salesEventFactory;
        $this->salesEventExtensionFactory = $salesEventExtensionFactory;
        $this->itemsToSellFactory = $itemsToSellFactory;
        $this->placeReservationsForSalesEvent = $placeReservationsForSalesEvent;
        $this->getSourceDeductedOrderItems = $getSourceDeductedOrderItems;
        $this->itemToDeductFactory = $itemToDeductFactory;
        $this->sourceDeductionRequestFactory = $sourceDeductionRequestFactory;
        $this->sourceDeductionService = $sourceDeductionService;
    }
    
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
    )
    {
        $deductedItems = $this->getSourceDeductedOrderItems->execute($order, $returnToStockItems);
        $itemsToSell = $backItemsPerSource = [];
        foreach ($itemsToRefund as $item) {
            $sku = $item->getSku();
            $sourceCode = (string) $item->getSourceCode();
            $qty = $item->getQuantity();
            $processedQty = $item->getProcessedQuantity() - $this->getTotalDeductedQty($item, $deductedItems);
            $qtyBackToSource = ($processedQty > 0) ? $qty - $processedQty : $qty;
            $qtyBackToStock = ($qtyBackToSource > 0) ? $qty - $qtyBackToSource : $qty;
            if ($qtyBackToStock > 0) {
                $itemToSell = $this->itemsToSellFactory->create([
                    'sku' => $sku,
                    'qty' => (float) $qtyBackToStock,
                ]);
                $itemToSell->getExtensionAttributes()->setSourceCode($sourceCode);
                $itemsToSell[] = $itemToSell;
            }
            foreach ($deductedItems as $deductedItemResult) {
                foreach ($deductedItemResult->getItems() as $deductedItem) {
                    if (
                        $sku != $deductedItem->getSku() || 
                        $sourceCode != $deductedItemResult->getSourceCode() || 
                        $this->isZero((float) $qtyBackToSource)
                    ) {
                        continue;
                    }
                    $backQty = min($deductedItem->getQuantity(), $qtyBackToSource);
                    $backItemsPerSource[$sourceCode][] = $this->itemToDeductFactory->create([
                        'sku' => $deductedItem->getSku(),
                        'qty' => -$backQty,
                    ]);
                    $qtyBackToSource -= $backQty;
                }
            }
        }
        $salesChannel = $this->salesChannelFactory->create((int) $order->getStore()->getWebsiteId());
        $salesEventExtension = $this->salesEventExtensionFactory->create(['data' => ['objectIncrementId' => (string) $order->getIncrementId()]]);
        $salesEvent = $this->salesEventFactory->create([
            'type' => \Magento\InventorySalesApi\Api\Data\SalesEventInterface::EVENT_CREDITMEMO_CREATED,
            'objectType' => \Magento\InventorySalesApi\Api\Data\SalesEventInterface::OBJECT_TYPE_ORDER,
            'objectId' => (string) $order->getEntityId(),
        ]);
        $salesEvent->setExtensionAttributes($salesEventExtension);
        foreach ($backItemsPerSource as $sourceCode => $items) {
            $sourceDeductionRequest = $this->sourceDeductionRequestFactory->create([
                'sourceCode' => (string) $sourceCode,
                'items' => $items,
                'salesChannel' => $salesChannel,
                'salesEvent' => $salesEvent,
            ]);
            $this->sourceDeductionService->execute($sourceDeductionRequest);
        }
        $this->placeReservationsForSalesEvent->execute($itemsToSell, $salesChannel, $salesEvent);
    }

    /**
     * Compare float number with some epsilon
     *
     * @param float $floatNumber
     * @return bool
     */
    private function isZero(float $floatNumber): bool
    {
        return $floatNumber < 0.0000001;
    }

    /**
     * Get total deducted qty
     * 
     * @param $item
     * @param array $deductedItems
     * @return float
     */
    private function getTotalDeductedQty($item, array $deductedItems): float
    {
        $result = 0;
        foreach ($deductedItems as $deductedItemResult) {
            foreach ($deductedItemResult->getItems() as $deductedItem) {
                if (
                    $item->getSku() != $deductedItem->getSku() || 
                    $item->getSourceCode() != $deductedItemResult->getSourceCode()
                ) {
                    continue;
                }
                $result += $deductedItem->getQuantity();
            }
        }
        return $result;
    }
}