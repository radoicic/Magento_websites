<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Observer\InventoryShipping;

/**
 * Source deduction processor observer plugin
 */
class SourceDeductionProcessor
{
    /**
     * Default source provider
     * 
     * @var \Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface
     */
    private $defaultSourceProvider;

    /**
     * Is single source mode
     * 
     * @var \Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface
     */
    private $isSingleSourceMode;

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
     * Get items to deduct from shipment
     * 
     * @var \Magento\InventoryShipping\Model\GetItemsToDeductFromShipment
     */
    private $getItemsToDeductFromShipment;

    /**
     * Source deduction request from shipment factory
     * 
     * @var \Magento\InventoryShipping\Model\SourceDeductionRequestFromShipmentFactory
     */
    private $sourceDeductionRequestFromShipmentFactory;

    /**
     * Source deduction service
     * 
     * @var \Magento\InventorySourceDeductionApi\Model\SourceDeductionServiceInterface
     */
    private $sourceDeductionService;

    /**
     * Constructor
     * 
     * @param \Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface $defaultSourceProvider
     * @param \Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface $isSingleSourceMode
     * @param \Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory $itemsToSellFactory
     * @param \Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface $placeReservationsForSalesEvent
     * @param \Magento\InventoryShipping\Model\GetItemsToDeductFromShipment $getItemsToDeductFromShipment
     * @param \Magento\InventoryShipping\Model\SourceDeductionRequestFromShipmentFactory $sourceDeductionRequestFromShipmentFactory
     * @param \Magento\InventorySourceDeductionApi\Model\SourceDeductionServiceInterface $sourceDeductionService
     * @return void
     */
    public function __construct(
        \Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface $defaultSourceProvider,
        \Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface $isSingleSourceMode,
        \Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory $itemsToSellFactory,
        \Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface $placeReservationsForSalesEvent,
        \Magento\InventoryShipping\Model\GetItemsToDeductFromShipment $getItemsToDeductFromShipment,
        \Magento\InventoryShipping\Model\SourceDeductionRequestFromShipmentFactory $sourceDeductionRequestFromShipmentFactory,
        \Magento\InventorySourceDeductionApi\Model\SourceDeductionServiceInterface $sourceDeductionService
    )
    {
        $this->defaultSourceProvider = $defaultSourceProvider;
        $this->isSingleSourceMode = $isSingleSourceMode;
        $this->itemsToSellFactory = $itemsToSellFactory;
        $this->placeReservationsForSalesEvent = $placeReservationsForSalesEvent;
        $this->getItemsToDeductFromShipment = $getItemsToDeductFromShipment;
        $this->sourceDeductionRequestFromShipmentFactory = $sourceDeductionRequestFromShipmentFactory;
        $this->sourceDeductionService = $sourceDeductionService;
    }
    
    /**
     * Around execute
     * 
     * @param \Magento\InventoryShipping\Observer\SourceDeductionProcessor $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function aroundExecute(
        \Magento\InventoryShipping\Observer\SourceDeductionProcessor $subject,
        \Closure $proceed,
        \Magento\Framework\Event\Observer $observer
    )
    {
        $shipment = $observer->getEvent()->getShipment();
        if ($shipment->getOrigData('entity_id')) {
            return;
        }
        if (!empty($shipment->getExtensionAttributes()) && !empty($shipment->getExtensionAttributes()->getSourceCode())) {
            $sourceCode = $shipment->getExtensionAttributes()->getSourceCode();
        } elseif ($this->isSingleSourceMode->execute()) {
            $sourceCode = $this->defaultSourceProvider->getCode();
        }
        $shipmentItems = $this->getItemsToDeductFromShipment->execute($shipment);
        if (!empty($shipmentItems)) {
            $sourceDeductionRequest = $this->sourceDeductionRequestFromShipmentFactory->execute($shipment, $sourceCode, $shipmentItems);
            $this->sourceDeductionService->execute($sourceDeductionRequest);
            $this->placeCompensatingReservation($sourceDeductionRequest);
        }
    }

    /**
     * Place compensating reservation
     * 
     * @param \Magento\InventorySourceDeductionApi\Model\SourceDeductionRequestInterface $sourceDeductionRequest
     * @return void
     */
    private function placeCompensatingReservation(\Magento\InventorySourceDeductionApi\Model\SourceDeductionRequestInterface $sourceDeductionRequest): void
    {
        $itemsToSell = [];
        $sourceCode = $sourceDeductionRequest->getSourceCode();
        foreach ($sourceDeductionRequest->getItems() as $item) {
            $itemToSell = $this->itemsToSellFactory->create(['sku' => $item->getSku(), 'qty' => $item->getQty()]);
            $itemToSell->getExtensionAttributes()->setSourceCode($sourceCode);
            $itemsToSell[] = $itemToSell;
        }
        $this->placeReservationsForSalesEvent->execute(
            $itemsToSell,
            $sourceDeductionRequest->getSalesChannel(),
            $sourceDeductionRequest->getSalesEvent()
        );
    }
}