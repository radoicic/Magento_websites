<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InventoryReservationCli\SalableQuantityInconsistency;

/**
 * Collector plugin
 */
class Collector
{
    /**
     * Wrapper factory
     * 
     * @var \Ecombricks\Common\DataObject\WrapperFactory
     */
    private $wrapperFactory;

    /**
     * Salable quantity inconsistency factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservationCli\SalableQuantityInconsistencyFactory
     */
    private $salableQuantityInconsistencyFactory;
    
    /**
     * Product metadata
     * 
     * @var \Magento\Framework\App\ProductMetadata 
     */
    private $productMetadata;

    /**
     * Serializer
     * 
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Magento\InventoryReservationCli\Model\ResourceModel\GetOrderIncrementId
     */
    private $getOrderIncrementId;

    /**
     * Items
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservationCli\SalableQuantityInconsistency[]
     */
    private $items = [];

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservationCli\SalableQuantityInconsistencyFactory $salableQuantityInconsistencyFactory
     * @param \Magento\Framework\App\ProductMetadata $productMetadata
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservationCli\SalableQuantityInconsistencyFactory $salableQuantityInconsistencyFactory,
        \Magento\Framework\App\ProductMetadata $productMetadata,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    )
    {
        $this->wrapperFactory = $wrapperFactory;
        $this->salableQuantityInconsistencyFactory = $salableQuantityInconsistencyFactory;
        $this->productMetadata = $productMetadata;
        $this->serializer = $serializer;
        if (version_compare($this->productMetadata->getVersion(), '2.4.0', '>=')) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->getOrderIncrementId = $objectManager->get(\Magento\InventoryReservationCli\Model\ResourceModel\GetOrderIncrementId::class);
        }
    }
    
    /**
     * Around add reservation
     * 
     * @param \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\Collector $subject
     * @param callable $proceed
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface $reservation
     * @return void
     */
    public function aroundAddReservation(
        $subject,
        callable $proceed,
        \Magento\InventoryReservationsApi\Model\ReservationInterface $reservation
    ): void
    {
        $metadata = $this->serializer->unserialize($reservation->getMetadata());
        $objectId = $metadata['object_id'];
        $sourceCode = (string) $reservation->getSourceCode();
        if (version_compare($this->productMetadata->getVersion(), '2.4.0', '>=')) {
            $objectIncrementId = !empty($metadata['object_increment_id']) ? $metadata['object_increment_id'] : $this->getOrderIncrementId->execute((int) $objectId);
            $key = $objectIncrementId.'-'.$sourceCode;
        } else {
            $key = $objectId.'-'.$sourceCode;
        }
        $salableQuantityInconsistency = $this->items[$key] ?? $this->salableQuantityInconsistencyFactory->create();
        $salableQuantityInconsistency->setObjectId((int) $objectId);
        if (version_compare($this->productMetadata->getVersion(), '2.4.0', '>=')) {
            $salableQuantityInconsistency->setOrderIncrementId((string) $objectIncrementId);
            $salableQuantityInconsistency->setHasAssignedOrder((int) $objectId || (string) $objectIncrementId);
        }
        $salableQuantityInconsistency->setSourceCode($sourceCode);
        $salableQuantityInconsistency->addItemQty($reservation->getSku(), $reservation->getQuantity());
        $this->items[$key] = $salableQuantityInconsistency;
    }

    /**
     * Add order to collectors items
     * 
     * @param \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\Collector $subject
     * @param callable $proceed
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return void
     */
    public function aroundAddOrder(
        $subject,
        callable $proceed,
        \Magento\Sales\Api\Data\OrderInterface $order
    ): void
    {
        $objectId = $order->getEntityId();
        $objectIncrementId = $order->getIncrementId();
        $orderWrapper = $this->wrapperFactory->create(
            $order,
            \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order::class
        );
        foreach ($orderWrapper->getSourceCodes() as $sourcecode) {
            if (version_compare($this->productMetadata->getVersion(), '2.4.0', '>=')) {
                $key = $objectIncrementId.'-'.$sourcecode;
            } else {
                $key = $objectId.'-'.$sourcecode;
            }
            $salableQuantityInconsistency = $this->items[$key] ?? $this->salableQuantityInconsistencyFactory->create();
            $salableQuantityInconsistency->setOrderIncrementId($objectIncrementId);
            if (version_compare($this->productMetadata->getVersion(), '2.4.0', '>=')) {
                $salableQuantityInconsistency->setHasAssignedOrder(true);
            }
            $salableQuantityInconsistency->setOrderStatus($order->getStatus());
            $this->items[$key] = $salableQuantityInconsistency;
        }
    }

    /**
     * Around add order data
     * 
     * @param \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\Collector $subject
     * @param callable $proceed
     * @param array $orderData
     * @return void
     */
    public function aroundAddOrderData(
        $subject,
        callable $proceed,
        array $orderData
    ): void
    {
        $objectId = $orderData['entity_id'] ?? null;
        $objectIncrementId = $orderData['increment_id'];
        if (empty($orderData['source_codes'])) {
            return;
        }
        foreach ($orderData['source_codes'] as $sourcecode) {
            if (version_compare($this->productMetadata->getVersion(), '2.4.0', '>=')) {
                $key = $objectIncrementId.'-'.$sourcecode;
            } else {
                $key = $objectId.'-'.$sourcecode;
            }
            $salableQuantityInconsistency = $this->items[$key] ?? $this->salableQuantityInconsistencyFactory->create();
            $salableQuantityInconsistency->setOrderIncrementId($objectIncrementId);
            if (version_compare($this->productMetadata->getVersion(), '2.4.0', '>=')) {
                $salableQuantityInconsistency->setHasAssignedOrder(true);
            }
            $salableQuantityInconsistency->setOrderStatus($orderData['status']);
            $this->items[$key] = $salableQuantityInconsistency;
        }
    }

    /**
     * Around get items
     * 
     * @param \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\Collector $subject
     * @param callable $proceed
     * @return \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservationCli\SalableQuantityInconsistency[]
     */
    public function aroundGetItems(
        $subject,
        callable $proceed
    ): array
    {
        return $this->items;
    }
    
    /**
     * Around set items
     * 
     * @param \Magento\InventoryReservationCli\Model\SalableQuantityInconsistency\Collector $subject
     * @param callable $proceed
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservationCli\SalableQuantityInconsistency[] $items
     * @return void
     */
    public function aroundSetItems(
        $subject,
        callable $proceed,
        array $items
    ): void
    {
        $this->items = $items;
    }
}