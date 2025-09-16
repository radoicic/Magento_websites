<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventoryShipping\ReturnProcessor;

/**
 * Get shipped items per source
 */
class GetShippedItemsPerSource implements \Magento\InventorySalesApi\Model\ReturnProcessor\GetSourceDeductedOrderItemsInterface
{
    /**
     * Get source code by shipment ID
     * 
     * @var \Magento\InventoryShipping\Model\ResourceModel\ShipmentSource\GetSourceCodeByShipmentId
     */
    private $getSourceCodeByShipmentId;

    /**
     * Source deducted order item factory
     * 
     * @var \Magento\InventorySalesApi\Model\ReturnProcessor\Result\SourceDeductedOrderItemFactory
     */
    private $sourceDeductedOrderItemFactory;

    /**
     * Source deducted order items result factory
     * 
     * @var \Magento\InventorySalesApi\Model\ReturnProcessor\Result\SourceDeductedOrderItemsResultFactory
     */
    private $sourceDeductedOrderItemsResultFactory;

    /**
     * Get items to deduct from shipment
     * 
     * @var \Magento\InventoryShipping\Model\GetItemsToDeductFromShipment
     */
    private $getItemsToDeductFromShipment;

    /**
     * Constructor
     * 
     * @param \Magento\InventoryShipping\Model\ResourceModel\ShipmentSource\GetSourceCodeByShipmentId $getSourceCodeByShipmentId
     * @param \Magento\InventorySalesApi\Model\ReturnProcessor\Result\SourceDeductedOrderItemFactory $sourceDeductedOrderItemFactory
     * @param \Magento\InventorySalesApi\Model\ReturnProcessor\Result\SourceDeductedOrderItemsResultFactory $sourceDeductedOrderItemsResultFactory
     * @param \Magento\InventoryShipping\Model\GetItemsToDeductFromShipment $getItemsToDeductFromShipment
     * @return void
     */
    public function __construct(
        \Magento\InventoryShipping\Model\ResourceModel\ShipmentSource\GetSourceCodeByShipmentId $getSourceCodeByShipmentId,
        \Magento\InventorySalesApi\Model\ReturnProcessor\Result\SourceDeductedOrderItemFactory $sourceDeductedOrderItemFactory,
        \Magento\InventorySalesApi\Model\ReturnProcessor\Result\SourceDeductedOrderItemsResultFactory $sourceDeductedOrderItemsResultFactory,
        \Magento\InventoryShipping\Model\GetItemsToDeductFromShipment $getItemsToDeductFromShipment
    )
    {
        $this->getSourceCodeByShipmentId = $getSourceCodeByShipmentId;
        $this->sourceDeductedOrderItemFactory = $sourceDeductedOrderItemFactory;
        $this->sourceDeductedOrderItemsResultFactory = $sourceDeductedOrderItemsResultFactory;
        $this->getItemsToDeductFromShipment = $getItemsToDeductFromShipment;
    }

    /**
     * Execute
     * 
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param array $returnToStockItems
     * @return \Magento\InventorySalesApi\Model\ReturnProcessor\Result\SourceDeductedOrderItemsResult[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Sales\Api\Data\OrderInterface $order, array $returnToStockItems): array
    {
        $qtys = [];
        foreach ($order->getShipmentsCollection() as $shipment) {
            $sourceCode = (string) $this->getSourceCodeByShipmentId->execute((int) $shipment->getId());
            $items = $this->getItemsToDeductFromShipment->execute($shipment);
            foreach ($items as $item) {
                $sku = $item->getSku();
                $qtys[$sourceCode][$sku] = ($qtys[$sourceCode][$sku] ?? 0) + $item->getQty();
            }
        }
        return $this->getSourceDeductedOrderItemsResults($qtys);
    }

    /**
     * Get source deducted order items results
     * 
     * @param array $qtys
     * @return \Magento\InventorySalesApi\Model\ReturnProcessor\Result\SourceDeductedOrderItemsResult[]
     */
    private function getSourceDeductedOrderItemsResults(array $qtys): array
    {
        $results = [];
        foreach ($qtys as $sourceCode => $sourceQtys) {
            $items = [];
            foreach ($sourceQtys as $sku => $qty) {
                $items[] = $this->sourceDeductedOrderItemFactory->create([
                    'sku' => $sku,
                    'quantity' => $qty,
                ]);
            }
            $results[] = $this->sourceDeductedOrderItemsResultFactory->create([
                'sourceCode' => (string) $sourceCode,
                'items' => $items,
            ]);
        }
        return $results;
    }
}