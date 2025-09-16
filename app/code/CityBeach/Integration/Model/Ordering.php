<?php

namespace CityBeach\Integration\Model;

use CityBeach\Integration\Api\OrderingInterface;
use CityBeach\Integration\Api\Data\OrderInterface;
use \Magento\Framework\ObjectManagerInterface;

class Ordering implements OrderingInterface
{
    /*
     * @var \Magento\Framework\ObjectManagerInterface $objectManager
     */
    private $objectManager;

    private $logger;

    public function __construct(
        ObjectManagerInterface $objectManager,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->objectManager = $objectManager;
        $this->logger = $logger;
    }

    /**
     * Create an order.
     *
     * @api
     * @param OrderInterface $order
     * @return string
     * @throws \Exception on a validation error or order processing failure.
     */
    public function execute(OrderInterface $customOrder) {
        $status = 'success';
        $message = '';
        $cart_order_id = null;
        $detail = '';

        try {
            /** @var $magentoQuoteBuilder \CityBeach\Integration\Model\OrderBuilder */
            $builder = $this->objectManager->create('\CityBeach\Integration\Model\OrderBuilder', ['customOrder' => $customOrder]);
            $builder->execute();
            $order = $builder->getOrder();
            $cart_order_id = $order->getId();
            $message = $builder->getMessage();
        } catch (\Exception $exception) {
            $this->logger->error('Error message', ['exception' => $exception]);
            $status = 'failure';
            $message = $exception->getMessage();
            $cart_order_id = null;
            $detail = $exception->getTraceAsString();
        }
        return array(
            'result' => array(
                'status' => $status,
                'message' => $message,
                'orderId' => $cart_order_id,
                'detail' => $detail,
            )
        );
    }
}
