<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventoryReservationCli\SalableQuantityInconsistency;

/**
 * Add existing reservations plugin
 */
class AddExistingReservations
{
    /**
     * Reservation builder
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationBuilderInterface
     */
    private $reservationBuilder;

    /**
     * Get reservations list
     * 
     * @var \Magento\InventoryReservationCli\Model\ResourceModel\GetReservationsList
     */
    private $getReservationsList;

    /**
     * Serializer
     * 
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationBuilderInterface $reservationBuilder
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\InventoryReservationCli\Model\ResourceModel\GetReservationsList $getReservationsList
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationBuilderInterface $reservationBuilder,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\InventoryReservationCli\Model\ResourceModel\GetReservationsList $getReservationsList
    )
    {
        $this->getReservationsList = $getReservationsList;
        $this->serializer = $serializer;
        $this->reservationBuilder = $reservationBuilder;
    }

    /**
     * Around execute
     * 
     * @param \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\AddExistingReservations $subject
     * @param callable $proceed
     * @param \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\Collector $collector
     * @return void
     * @throws \Magento\Framework\Validation\ValidationException
     */
    public function aroundExecute(
        \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\AddExistingReservations $subject,
        callable $proceed,
        \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\Collector $collector
    ): void
    {
        $reservationList = $this->getReservationsList->execute();
        foreach ($reservationList as $reservation) {
            $metadata = $this->serializer->unserialize($reservation['metadata']);
            $orderType = $metadata['object_type'];
            if ($orderType !== 'order') {
                continue;
            }
            $reservation = $this->reservationBuilder
                ->setMetadata($reservation['metadata'])
                ->setSourceCode($reservation['source_code'])
                ->setSku($reservation['sku'])
                ->setQuantity((float) $reservation['quantity'])
                ->build();
            $collector->addReservation($reservation);
        }
    }
}