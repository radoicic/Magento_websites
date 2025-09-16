<?php

namespace CityBeach\Integration\Api;
use CityBeach\Integration\Api\Data\OrderInterface;

interface OrderingInterface
{
    /**
     * Create an order.
     *
     * @api
     * @param \CityBeach\Integration\Api\Data\OrderInterface $order
     * @return string
     */
    public function execute(OrderInterface $order);
}
