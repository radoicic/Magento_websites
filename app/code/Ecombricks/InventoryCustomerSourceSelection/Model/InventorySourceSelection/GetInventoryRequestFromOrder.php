<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventorySourceSelection;

/**
 * Get inventory request from order
 */
class GetInventoryRequestFromOrder
{
    /**
     * Product metadata
     * 
     * @var \Magento\Framework\App\ProductMetadata 
     */
    private $productMetadata;
    
    /**
     * Stock by website ID resolver
     * 
     * @var \Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface
     */
    private $stockByWebsiteIdResolver;
    
    /**
     * Inventory request factory
     * 
     * @var \Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterfaceFactory
     */
    private $inventoryRequestFactory;
    
    /**
     * Order repository
     * 
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;
    
    /**
     * Store manager
     * 
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    
    /**
     * Inventory request extension factory
     * 
     * @var \Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestExtensionInterfaceFactory
     */
    private $inventoryRequestExtensionFactory;

    /**
     * Address interface factory
     * 
     * @var \Magento\InventorySourceSelectionApi\Api\Data\AddressInterfaceFactory
     */
    private $addressInterfaceFactory;

    /**
     * Constructor
     * 
     * @param \Magento\Framework\App\ProductMetadata $productMetadata
     * @param \Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver
     * @param \Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterfaceFactory $inventoryRequestFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\ProductMetadata $productMetadata,
        \Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver,
        \Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterfaceFactory $inventoryRequestFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->productMetadata = $productMetadata;
        $this->stockByWebsiteIdResolver = $stockByWebsiteIdResolver;
        $this->inventoryRequestFactory = $inventoryRequestFactory;
        $this->orderRepository = $orderRepository;
        $this->storeManager = $storeManager;
        if (version_compare($this->productMetadata->getVersion(), '2.3.1', '>=')) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->inventoryRequestExtensionFactory = $objectManager->get(\Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestExtensionInterfaceFactory::class);
            $this->addressInterfaceFactory = $objectManager->get(\Magento\InventorySourceSelectionApi\Api\Data\AddressInterfaceFactory::class);
        }
    }

    /**
     * Execute
     *
     * @param int $orderId
     * @param array $requestItems
     */
    public function execute(int $orderId, array $requestItems): \Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterface
    {
        $order = $this->orderRepository->get($orderId);
        $store = $this->storeManager->getStore($order->getStoreId());
        $stock = $this->stockByWebsiteIdResolver->execute((int) $store->getWebsiteId());
        $inventoryRequest = $this->inventoryRequestFactory->create([ 'stockId' => $stock->getStockId(), 'items' => $requestItems, ]);
        if (version_compare($this->productMetadata->getVersion(), '2.3.1', '>=')) {
            $address = $this->getAddressFromOrder($order);
            if ($address !== null) {
                $extensionAttributes = $this->inventoryRequestExtensionFactory->create();
                $extensionAttributes->setDestinationAddress($address);
                $inventoryRequest->setExtensionAttributes($extensionAttributes);
            }
        }
        return $inventoryRequest;
    }

    /**
     * Get address from order
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\InventorySourceSelectionApi\Api\Data\AddressInterface|null
     */
    private function getAddressFromOrder(\Magento\Sales\Api\Data\OrderInterface $order): ?\Magento\InventorySourceSelectionApi\Api\Data\AddressInterface
    {
        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress === null) {
            return null;
        }
        return $this->addressInterfaceFactory->create(
            [
                'country' => $shippingAddress->getCountryId(),
                'postcode' => $shippingAddress->getPostcode() ?? '',
                'street' => implode("\n", $shippingAddress->getStreet()),
                'region' => $shippingAddress->getRegion() ?? $shippingAddress->getRegionCode() ?? '',
                'city' => $shippingAddress->getCity() ?? '',
            ]
        );
    }
}