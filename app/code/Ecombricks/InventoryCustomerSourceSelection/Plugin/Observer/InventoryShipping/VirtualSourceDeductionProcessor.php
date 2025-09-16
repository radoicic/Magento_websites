<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Observer\InventoryShipping;

/**
 * Virtual source deduction processor observer plugin
 */
class VirtualSourceDeductionProcessor
{
    /**
     * Item to sell factory
     * 
     * @var \Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory
     */
    private $itemToSellFactory;

    /**
     * Sales event extension factory
     * 
     * @var \Magento\InventorySalesApi\Api\Data\SalesEventExtensionFactory 
     */
    private $salesEventExtensionFactory;

    /**
     * Sales event factory
     * 
     * @var \Magento\InventorySalesApi\Api\Data\SalesEventInterfaceFactory
     */
    private $salesEventFactory;

    /**
     * Place reservations for sales event
     * 
     * @var \Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface
     */
    private $placeReservationsForSalesEvent;

    /**
     * Get source selection result from invoice
     * 
     * @var \Magento\InventoryShipping\Model\GetSourceSelectionResultFromInvoice
     */
    private $getSourceSelectionResultFromInvoice;

    /**
     * Source deduction requests from source selection factory
     * 
     * @var \Magento\InventoryShipping\Model\SourceDeductionRequestsFromSourceSelectionFactory
     */
    private $sourceDeductionRequestsFromSourceSelectionFactory;

    /**
     * Source deduction service
     * 
     * @var \Magento\InventorySourceDeductionApi\Model\SourceDeductionServiceInterface
     */
    private $sourceDeductionService;

    /**
     * Constructor
     * 
     * @param \Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory $itemToSellFactory
     * @param \Magento\InventorySalesApi\Api\Data\SalesEventExtensionFactory $salesEventExtensionFactory
     * @param \Magento\InventorySalesApi\Api\Data\SalesEventInterfaceFactory $salesEventFactory
     * @param \Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface $placeReservationsForSalesEvent
     * @param \Magento\InventoryShipping\Model\GetSourceSelectionResultFromInvoice $getSourceSelectionResultFromInvoice
     * @param \Magento\InventoryShipping\Model\SourceDeductionRequestsFromSourceSelectionFactory $sourceDeductionRequestsFromSourceSelectionFactory
     * @param \Magento\InventorySourceDeductionApi\Model\SourceDeductionServiceInterface $sourceDeductionService
     * @return void
     */
    public function __construct(
        \Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory $itemToSellFactory,
        \Magento\InventorySalesApi\Api\Data\SalesEventExtensionFactory $salesEventExtensionFactory,
        \Magento\InventorySalesApi\Api\Data\SalesEventInterfaceFactory $salesEventFactory,
        \Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface $placeReservationsForSalesEvent,
        \Magento\InventoryShipping\Model\GetSourceSelectionResultFromInvoice $getSourceSelectionResultFromInvoice,
        \Magento\InventoryShipping\Model\SourceDeductionRequestsFromSourceSelectionFactory $sourceDeductionRequestsFromSourceSelectionFactory,
        \Magento\InventorySourceDeductionApi\Model\SourceDeductionServiceInterface $sourceDeductionService
    )
    {
        $this->itemToSellFactory = $itemToSellFactory;
        $this->salesEventExtensionFactory = $salesEventExtensionFactory;
        $this->salesEventFactory = $salesEventFactory;
        $this->placeReservationsForSalesEvent = $placeReservationsForSalesEvent;
        $this->getSourceSelectionResultFromInvoice = $getSourceSelectionResultFromInvoice;
        $this->sourceDeductionRequestsFromSourceSelectionFactory = $sourceDeductionRequestsFromSourceSelectionFactory;
        $this->sourceDeductionService = $sourceDeductionService;
    }
    
    /**
     * Around execute
     * 
     * @param \Magento\InventoryShipping\Observer\VirtualSourceDeductionProcessor $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function aroundExecute(
        \Magento\InventoryShipping\Observer\VirtualSourceDeductionProcessor $subject,
        \Closure $proceed,
        \Magento\Framework\Event\Observer $observer
    )
    {
        $invoice = $observer->getEvent()->getInvoice();
        if (!$this->isValid($invoice)) {
            return;
        }
        $sourceSelectionResult = $this->getSourceSelectionResultFromInvoice->execute($invoice);
        $salesEventExtension = $this->salesEventExtensionFactory->create([
            'data' => ['objectIncrementId' => (string) $invoice->getOrder()->getIncrementId()]
        ]);
        $salesEvent = $this->salesEventFactory->create([
            'type' => \Magento\InventorySalesApi\Api\Data\SalesEventInterface::EVENT_INVOICE_CREATED,
            'objectType' => \Magento\InventorySalesApi\Api\Data\SalesEventInterface::OBJECT_TYPE_ORDER,
            'objectId' => $invoice->getOrderId(),
        ]);
        $salesEvent->setExtensionAttributes($salesEventExtension);
        $sourceDeductionRequests = $this->sourceDeductionRequestsFromSourceSelectionFactory->create(
            $sourceSelectionResult,
            $salesEvent,
            (int) $invoice->getOrder()->getStore()->getWebsiteId()
        );
        foreach ($sourceDeductionRequests as $sourceDeductionRequest) {
            $this->sourceDeductionService->execute($sourceDeductionRequest);
            $this->placeCompensatingReservation($sourceDeductionRequest);
        }
    }
    
    /**
     * Check if has valid items
     * 
     * @param \Magento\Sales\Api\Data\InvoiceInterface $invoice
     * @return bool
     */
    private function hasValidItems(\Magento\Sales\Api\Data\InvoiceInterface $invoice): bool
    {
        foreach ($invoice->getItems() as $invoiceItem) {
            $orderItem = $invoiceItem->getOrderItem();
            if ($orderItem->getIsVirtual()) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Check if is valid
     * 
     * @param \Magento\Sales\Api\Data\InvoiceInterface $invoice
     * @return bool
     */
    private function isValid(\Magento\Sales\Api\Data\InvoiceInterface $invoice): bool
    {
        if ($invoice->getOrigData('entity_id')) {
            return false;
        }
        return $this->hasValidItems($invoice);
    }
    
    /**
     * Place compensating reservation
     *
     * @param \Magento\InventorySourceDeductionApi\Model\SourceDeductionRequestInterface $sourceDeductionRequest
     */
    private function placeCompensatingReservation(\Magento\InventorySourceDeductionApi\Model\SourceDeductionRequestInterface $sourceDeductionRequest): void
    {
        $itemsToSell = [];
        $sourceCode = (string) $sourceDeductionRequest->getSourceCode();
        foreach ($sourceDeductionRequest->getItems() as $item) {
            $itemToSell = $this->itemToSellFactory->create(['sku' => $item->getSku(), 'qty' => $item->getQty()]);
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