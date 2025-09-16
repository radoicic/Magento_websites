<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Api\Sales;

/**
 * Order management interface plugin
 */
class OrderManagementInterface
{
    /**
     * Sales channel factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales\Data\WebsiteSalesChannelInterfaceFactory
     */
    private $salesChannelFactory;

    /**
     * Check items quantity
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\CheckItemsQuantity
     */
    private $checkItemsQuantity;

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
     * Get SKUs by product IDs
     * 
     * @var \Magento\InventoryCatalogApi\Model\GetSkusByProductIdsInterface 
     */
    private $getSkusByProductIds;

    /**
     * Is source item management allowed for product type
     * 
     * @var \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface 
     */
    private $isSourceItemManagementAllowedForProductType;

    /**
     * Items to sell factory
     * 
     * @var \Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory
     */
    private $itemsToSellFactory;

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
     * Product metadata
     * 
     * @var \Magento\Framework\App\ProductMetadata
     */
    private $productMetadata;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales\Data\WebsiteSalesChannelInterfaceFactory $salesChannelFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\CheckItemsQuantity $checkItemsQuantity
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory $orderItemWrapperFactory
     * @param \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface $getProductTypesBySkus
     * @param \Magento\InventoryCatalogApi\Model\GetSkusByProductIdsInterface $getSkusByProductIds
     * @param \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param \Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory $itemsToSellFactory
     * @param \Magento\InventorySalesApi\Api\Data\SalesEventExtensionFactory $salesEventExtensionFactory
     * @param \Magento\InventorySalesApi\Api\Data\SalesEventInterfaceFactory $salesEventFactory
     * @param \Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface $placeReservationsForSalesEvent
     * @param \Magento\Framework\App\ProductMetadata $productMetadata
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales\Data\WebsiteSalesChannelInterfaceFactory $salesChannelFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\CheckItemsQuantity $checkItemsQuantity,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory $orderItemWrapperFactory,
        \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface $getProductTypesBySkus,
        \Magento\InventoryCatalogApi\Model\GetSkusByProductIdsInterface $getSkusByProductIds,
        \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        \Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory $itemsToSellFactory,
        \Magento\InventorySalesApi\Api\Data\SalesEventExtensionFactory $salesEventExtensionFactory,
        \Magento\InventorySalesApi\Api\Data\SalesEventInterfaceFactory $salesEventFactory,
        \Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface $placeReservationsForSalesEvent,
        \Magento\Framework\App\ProductMetadata $productMetadata
    )
    {
        $this->salesChannelFactory = $salesChannelFactory;
        $this->checkItemsQuantity = $checkItemsQuantity;
        $this->orderItemWrapperFactory = $orderItemWrapperFactory;
        $this->getProductTypesBySkus = $getProductTypesBySkus;
        $this->getSkusByProductIds = $getSkusByProductIds;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->itemsToSellFactory = $itemsToSellFactory;
        $this->salesEventExtensionFactory = $salesEventExtensionFactory;
        $this->salesEventFactory = $salesEventFactory;
        $this->placeReservationsForSalesEvent = $placeReservationsForSalesEvent;
        $this->productMetadata = $productMetadata;
    }
    
    /**
     * Around place
     *
     * @param \Magento\Sales\Api\OrderManagementInterface $subject
     * @param callable $proceed
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws \Exception
     */
    public function aroundPlace(
        \Magento\Sales\Api\OrderManagementInterface $subject,
        callable $proceed,
        \Magento\Sales\Api\Data\OrderInterface $order
    ): \Magento\Sales\Api\Data\OrderInterface
    {
        if (version_compare($this->productMetadata->getVersion(), '2.4.0', '<')) {
            $order = $proceed($order);
        }
        $itemsById = $itemsBySku = $itemsToSell = [];
        foreach ($order->getItems() as $item) {
            $productId = (int) $item->getProductId();
            $sourceCode = (string) $this->orderItemWrapperFactory->create($item)->getSourceCode();
            if (!isset($itemsById[$productId][$sourceCode])) {
                $itemsById[$productId][$sourceCode] = 0;
            }
            $itemsById[$productId][$sourceCode] += $item->getQtyOrdered();
        }
        $skus = $this->getSkusByProductIds->execute(array_keys($itemsById));
        $productTypes = $this->getProductTypesBySkus->execute($skus);
        foreach ($skus as $productId => $sku) {
            if (false === $this->isSourceItemManagementAllowedForProductType->execute($productTypes[$sku])) {
                continue;
            }
            $itemsBySku[$sku] = $itemsById[$productId];
            foreach ($itemsBySku[$sku] as $sourceCode => $qty) {
                $itemToSell = $this->itemsToSellFactory->create(['sku' => $sku, 'qty' => - (float) $qty]);
                $itemToSell->getExtensionAttributes()->setSourceCode((string) $sourceCode);
                $itemsToSell[] = $itemToSell;
            }
        }
        $this->checkItemsQuantity->execute($itemsBySku);
        $salesChannel = $this->salesChannelFactory->create((int) $order->getStore()->getWebsiteId());
        $salesEventExtension = $this->salesEventExtensionFactory->create([ 'data' => [ 'objectIncrementId' => (string) $order->getIncrementId(), ], ]);
        $salesEvent = $this->salesEventFactory->create([
            'type' => \Magento\InventorySalesApi\Api\Data\SalesEventInterface::EVENT_ORDER_PLACED,
            'objectType' => \Magento\InventorySalesApi\Api\Data\SalesEventInterface::OBJECT_TYPE_ORDER,
            'objectId' => (string) $order->getEntityId(),
        ]);
        $salesEvent->setExtensionAttributes($salesEventExtension);
        $this->placeReservationsForSalesEvent->execute($itemsToSell, $salesChannel, $salesEvent);
        if (version_compare($this->productMetadata->getVersion(), '2.4.0', '>=')) {
            try {
                $order = $proceed($order);
            } catch (\Exception $exception) {
                foreach ($itemsToSell as $item) {
                    $item->setQuantity(-(float) $item->getQuantity());
                }
                $salesEvent = $this->salesEventFactory->create([
                    'type' => \Magento\InventorySalesApi\Api\Data\SalesEventInterface::EVENT_ORDER_PLACE_FAILED,
                    'objectType' => \Magento\InventorySalesApi\Api\Data\SalesEventInterface::OBJECT_TYPE_ORDER,
                    'objectId' => (string) $order->getEntityId(),
                ]);
                $salesEvent->setExtensionAttributes($salesEventExtension);
                $this->placeReservationsForSalesEvent->execute($itemsToSell, $salesChannel, $salesEvent);
                throw $exception;
            }
        }
        return $order;
    }
}