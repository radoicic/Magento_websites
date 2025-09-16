<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventoryShipping;

/**
 * Get source selection result from invoice plugin
 */
class GetSourceSelectionResultFromInvoice extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Get inventory request from order
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySourceSelection\GetInventoryRequestFromOrder 
     */
    private $getInventoryRequestFromOrder;

    /**
     * Get SKU from order item
     * 
     * @var \Magento\InventorySalesApi\Model\GetSkuFromOrderItemInterface
     */
    private $getSkuFromOrderItem;

    /**
     * Item request factory
     * 
     * @var \Magento\InventorySourceSelectionApi\Api\Data\ItemRequestInterfaceFactory
     */
    private $itemRequestFactory;

    /**
     * Get default source selection algorithm code
     * 
     * @var \Magento\InventorySourceSelectionApi\Api\GetDefaultSourceSelectionAlgorithmCodeInterface
     */
    private $getDefaultSourceSelectionAlgorithmCode;

    /**
     * Source selection service
     * 
     * @var \Magento\InventorySourceSelectionApi\Api\SourceSelectionServiceInterface
     */
    private $sourceSelectionService;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySourceSelection\GetInventoryRequestFromOrder $getInventoryRequestFromOrder
     * @param \Magento\InventorySalesApi\Model\GetSkuFromOrderItemInterface $getSkuFromOrderItem
     * @param \Magento\InventorySourceSelectionApi\Api\Data\ItemRequestInterfaceFactory $itemRequestFactory
     * @param \Magento\InventorySourceSelectionApi\Api\GetDefaultSourceSelectionAlgorithmCodeInterface $getDefaultSourceSelectionAlgorithmCode
     * @param \Magento\InventorySourceSelectionApi\Api\SourceSelectionServiceInterface $sourceSelectionService
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySourceSelection\GetInventoryRequestFromOrder $getInventoryRequestFromOrder,
        \Magento\InventorySalesApi\Model\GetSkuFromOrderItemInterface $getSkuFromOrderItem,
        \Magento\InventorySourceSelectionApi\Api\Data\ItemRequestInterfaceFactory $itemRequestFactory,
        \Magento\InventorySourceSelectionApi\Api\GetDefaultSourceSelectionAlgorithmCodeInterface $getDefaultSourceSelectionAlgorithmCode,
        \Magento\InventorySourceSelectionApi\Api\SourceSelectionServiceInterface $sourceSelectionService
    )
    {
        parent::__construct($wrapperFactory);
        $this->getInventoryRequestFromOrder = $getInventoryRequestFromOrder;
        $this->getSkuFromOrderItem = $getSkuFromOrderItem;
        $this->itemRequestFactory = $itemRequestFactory;
        $this->getDefaultSourceSelectionAlgorithmCode = $getDefaultSourceSelectionAlgorithmCode;
        $this->sourceSelectionService = $sourceSelectionService;
    }

    /**
     * Around execute
     * 
     * @param \Magento\InventoryShipping\Model\GetSourceSelectionResultFromInvoice $subject
     * @param \Closure $proceed
     * @param \Magento\Sales\Api\Data\InvoiceInterface $invoice
     * @return \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterface
     */
    public function aroundExecute(
        \Magento\InventoryShipping\Model\GetSourceSelectionResultFromInvoice $subject,
        \Closure $proceed,
        \Magento\Sales\Api\Data\InvoiceInterface $invoice
    ): \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterface
    {
        $this->setSubject($subject);
        return $this->sourceSelectionService->execute(
            $this->getInventoryRequestFromOrder->execute(
                (int) $invoice->getOrder()->getEntityId(),
                $this->getItemRequests($invoice->getItems())
            ),
            $this->getDefaultSourceSelectionAlgorithmCode->execute()
        );
    }
    
    /**
     * Get item requests
     * 
     * @param \Magento\Sales\Api\Data\InvoiceItemInterface[]|iterable $invoiceItems
     * @return array
     */
    private function getItemRequests(iterable $invoiceItems): array
    {
        $itemRequests = [];
        foreach ($invoiceItems as $invoiceItem) {
            $orderItem = $invoiceItem->getOrderItem();
            if ($orderItem->isDummy() || !$orderItem->getIsVirtual()) {
                continue;
            }
            $sku = $this->getSkuFromOrderItem->execute($orderItem);
            $qty = $this->invokeSubjectMethod('castQty', $invoiceItem->getOrderItem(), $invoiceItem->getQty());
            $itemRequest = $this->itemRequestFactory->create([
                'sku' => $sku,
                'qty' => $qty,
            ]);
            $itemRequest->getExtensionAttributes()->setOrderItemId((int) $orderItem->getId());
            $itemRequests[] = $itemRequest;
        }
        return $itemRequests;
    }
}