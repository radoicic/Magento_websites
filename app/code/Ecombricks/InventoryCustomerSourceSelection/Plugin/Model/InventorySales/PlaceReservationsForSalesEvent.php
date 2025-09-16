<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventorySales;

/**
 * Place reservations for sales event plugin
 */
class PlaceReservationsForSalesEvent
{
    /**
     * Reservation builder
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationBuilderInterface
     */
    private $reservationBuilder;

    /**
     * Serializer
     * 
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

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
     * Append reservations
     * 
     * @var \Magento\InventoryReservationsApi\Model\AppendReservationsInterface
     */
    private $appendReservations;

    /**
     * Sales event to array converter
     * 
     * @var \Magento\InventorySales\Model\SalesEventToArrayConverter
     */
    private $salesEventToArrayConverter;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationBuilderInterface $reservationBuilder
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface $getProductTypesBySkus
     * @param \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param \Magento\InventoryReservationsApi\Model\AppendReservationsInterface $appendReservations
     * @param \Magento\InventorySales\Model\SalesEventToArrayConverter $salesEventToArrayConverter
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationBuilderInterface $reservationBuilder,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface $getProductTypesBySkus,
        \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        \Magento\InventoryReservationsApi\Model\AppendReservationsInterface $appendReservations,
        \Magento\InventorySales\Model\SalesEventToArrayConverter $salesEventToArrayConverter
    )
    {
        $this->reservationBuilder = $reservationBuilder;
        $this->serializer = $serializer;
        $this->getProductTypesBySkus = $getProductTypesBySkus;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->appendReservations = $appendReservations;
        $this->salesEventToArrayConverter = $salesEventToArrayConverter;
    }
    
    /**
     * Around execute
     * 
     * @param \Magento\InventorySales\Model\PlaceReservationsForSalesEvent $subject
     * @param callable $proceed
     * @param array $items
     * @param \Magento\InventorySalesApi\Api\Data\SalesChannelInterface $salesChannel
     * @param \Magento\InventorySalesApi\Api\Data\SalesEventInterface $salesEvent
     * @return void
     */
    public function aroundExecute(
        \Magento\InventorySales\Model\PlaceReservationsForSalesEvent $subject,
        callable $proceed,
        array $items, 
        \Magento\InventorySalesApi\Api\Data\SalesChannelInterface $salesChannel, 
        \Magento\InventorySalesApi\Api\Data\SalesEventInterface $salesEvent
    ): void
    {
        if (empty($items)) {
            return;
        }
        $skus = [];
        foreach ($items as $item) {
            $skus[] = $item->getSku();
        }
        $productTypes = $this->getProductTypesBySkus->execute($skus);
        $reservations = [];
        foreach ($items as $item) {
            $sku = $item->getSku();
            $skuNotExistInCatalog = !isset($productTypes[$sku]);
            if (!$skuNotExistInCatalog && !$this->isSourceItemManagementAllowedForProductType->execute($productTypes[$sku])) {
                continue;
            }
            $sourceCode = (string) $item->getExtensionAttributes()->getSourceCode();
            $quantity = (float) $item->getQuantity();
            $reservations[] = $this->reservationBuilder
                ->setSku($sku)
                ->setSourceCode($sourceCode)
                ->setQuantity($quantity)
                ->setMetadata($this->serializer->serialize($this->salesEventToArrayConverter->execute($salesEvent)))
                ->build();
        }
        $this->appendReservations->execute($reservations);
    }
}