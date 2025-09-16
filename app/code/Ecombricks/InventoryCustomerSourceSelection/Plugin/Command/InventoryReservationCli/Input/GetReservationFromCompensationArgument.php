<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Command\InventoryReservationCli\Input;

/**
 * Get reservation from compensation argument plugin
 */
class GetReservationFromCompensationArgument
{
    /**
     * Reservation builder
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationBuilderInterface
     */
    private $reservationBuilder;
    
    /**
     * Search criteria builder
     * 
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Serializer
     * 
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * Order repository
     * 
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationBuilderInterface $reservationBuilder
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationBuilderInterface $reservationBuilder,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    )
    {
        $this->reservationBuilder = $reservationBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->serializer = $serializer;
        $this->orderRepository = $orderRepository;
    }
    
    /**
     * Around execute
     * 
     * @param \Magento\InventoryReservationCli\Command\Input\GetReservationFromCompensationArgument $subject
     * @param callable $proceed
     * @param string $argument
     * @return \Magento\InventoryReservationsApi\Model\ReservationInterface\ReservationInterface
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Magento\Framework\Validation\ValidationException
     */
    public function aroundExecute(
        \Magento\InventoryReservationCli\Command\Input\GetReservationFromCompensationArgument $subject,
        callable $proceed,
        string $argument
    ): \Magento\InventoryReservationsApi\Model\ReservationInterface\ReservationInterface
    {
        $argumentParts = $this->parseArgument($argument);
        $results = $this->orderRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter('increment_id', $argumentParts['increment_id'], 'eq')
                ->create()
        );
        $order = current($results->getItems());
        return $this->reservationBuilder
            ->setSku((string) $argumentParts['sku'])
            ->setQuantity((float) $argumentParts['quantity'])
            ->setSourceCode((string) $argumentParts['source_code'])
            ->setMetadata(
                $this->serializer->serialize(
                    [
                        'event_type' => 'manual_compensation',
                        'object_type' => 'order',
                        'object_id' => $order->getEntityId(),
                        'object_increment_id' => $order->getIncrementId(),
                    ]
                )
            )
            ->build();
    }

    /**
     * Parse argument
     *
     * @param string $argument
     * @return array
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    private function parseArgument(string $argument): array
    {
        $pattern = '/(?P<increment_id>.*):(?P<sku>.*):(?P<quantity>.*):(?P<source_code>.*)/';
        $match = [];
        if (preg_match($pattern, $argument, $match)) {
            return $match;
        }
        throw new \Symfony\Component\Console\Exception\InvalidArgumentException(sprintf('Given argument does not match pattern "%s"', $pattern));
    }
}