<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Sales\Order;

/**
 * Order shipment factory plugin
 */
class ShipmentFactory
{
    /**
     * Current source provider
     * 
     * @var \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface
     */
    private $currentSourceProvider;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface $currentSourceProvider
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface $currentSourceProvider
    )
    {
        $this->currentSourceProvider = $currentSourceProvider;
    }
    
    /**
     * After create
     * 
     * @param \Magento\Sales\Model\Order\ShipmentFactory $subject
     * @param \Magento\Sales\Api\Data\ShipmentInterface $shipment
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Sales\Api\Data\ShipmentInterface
     */
    public function afterCreate(
        \Magento\Sales\Model\Order\ShipmentFactory $subject,
        \Magento\Sales\Api\Data\ShipmentInterface $shipment,
        \Magento\Sales\Model\Order $order
    )
    {
        $shipmentExtension = $shipment->getExtensionAttributes();
        if (!empty($shipmentExtension)) {
            $sourceCode = (string) $shipmentExtension->getSourceCode();
        } else {
            $sourceCode = null;
        }
        $this->currentSourceProvider->setSourceCode($sourceCode);
        return $shipment;
    }
}