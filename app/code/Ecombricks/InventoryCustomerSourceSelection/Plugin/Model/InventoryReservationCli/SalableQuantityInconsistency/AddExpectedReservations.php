<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventoryReservationCli\SalableQuantityInconsistency;

/**
 * Add expected reservations plugin
 */
class AddExpectedReservations
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
     * Get order items data for order in not final state
     * 
     * @var \Magento\InventoryReservationCli\Model\ResourceModel\GetOrderItemsDataForOrdersInNotFinalState
     */
    private $getOrderItemsDataForOrderInNotFinalState;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationBuilderInterface $reservationBuilder
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\InventoryReservationCli\Model\ResourceModel\GetOrderItemsDataForOrdersInNotFinalState $getOrderItemsDataForOrderInNotFinalState
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationBuilderInterface $reservationBuilder,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\InventoryReservationCli\Model\ResourceModel\GetOrderItemsDataForOrdersInNotFinalState $getOrderItemsDataForOrderInNotFinalState
    )
    {
        $this->reservationBuilder = $reservationBuilder;
        $this->serializer = $serializer;
        $this->getOrderItemsDataForOrderInNotFinalState = $getOrderItemsDataForOrderInNotFinalState;
    }
    
    /**
     * Around execute
     * 
     * @param \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\AddExpectedReservations $subject
     * @param callable $proceed
     * @param \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\Collector $collector
     * @param int $bunchSize
     * @param int $page
     * @return void
     * @throws \Magento\Framework\Validation\ValidationException
     */
    public function aroundExecute(
        \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\AddExpectedReservations $subject,
        callable $proceed,
        \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\Collector $collector,
        int $bunchSize = 50,
        int $page = 1
    ): void
    {
        foreach ($this->getOrderItemsDataForOrderInNotFinalState->execute($bunchSize, $page) as $orderItemData) {
            foreach ($orderItemData['source_codes'] as $sourceCode) {
                $reservation = $this->reservationBuilder
                    ->setSku($orderItemData['sku'])
                    ->setQuantity((float) $orderItemData['qty_ordered'])
                    ->setSourceCode((string) $sourceCode)
                    ->setMetadata($this->serializer->serialize(
                        [
                            'object_id' => (int)$orderItemData['entity_id'],
                            'object_increment_id' => (string)$orderItemData['increment_id'],
                        ]
                    ))
                    ->build();
                $collector->addReservation($reservation);
                $collector->addOrderData($orderItemData);
            }
        }
    }
}