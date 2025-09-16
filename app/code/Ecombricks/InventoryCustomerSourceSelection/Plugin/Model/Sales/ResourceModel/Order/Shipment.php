<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Sales\ResourceModel\Order;

/**
 * Order shipment resource plugin
 */
class Shipment
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
     * After load
     * 
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment $subject
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment $result
     * @param \Magento\Framework\Model\AbstractModel $shipment
     * @return \Magento\Sales\Model\ResourceModel\Order\Shipment
     */
    public function afterLoad(
        \Magento\Sales\Model\ResourceModel\Order\Shipment $subject,
        \Magento\Sales\Model\ResourceModel\Order\Shipment $result,
        \Magento\Framework\Model\AbstractModel $shipment
    )
    {
        $shipmentExtension = $shipment->getExtensionAttributes();
        if (!empty($shipmentExtension)) {
            $sourceCode = $shipmentExtension->getSourceCode();
        } else {
            $sourceCode = null;
        }
        $this->currentSourceProvider->setSourceCode($sourceCode);
        return $result;
    }
}