<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Shipping;

/**
 * Carrier factory plugin
 */
class CarrierFactory
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
     * Before get
     * 
     * @param \Magento\Shipping\Model\CarrierFactory $subject
     * @param array|string $carrierCode
     * @return bool|\Magento\Shipping\Model\Carrier\AbstractCarrier
     */
    public function beforeGet(
        \Magento\Shipping\Model\CarrierFactory $subject,
        $carrierCode
    )
    {
        return [$this->getSourceCarrierCode($carrierCode)];
    }

    /**
     * Before create
     * 
     * @param \Magento\Shipping\Model\CarrierFactory $subject
     * @param array|string $carrierCode
     * @param int|null $storeId
     * @return bool|\Magento\Shipping\Model\Carrier\AbstractCarrier
     */
    public function beforeCreate(
        \Magento\Shipping\Model\CarrierFactory $subject,
        $carrierCode,
        $storeId = null
    )
    {
        return [$this->getSourceCarrierCode($carrierCode), $storeId];
    }

    /**
     * Before get if active
     * 
     * @param \Magento\Shipping\Model\CarrierFactory $subject
     * @param array|string $carrierCode
     * @return bool|\Magento\Shipping\Model\Carrier\AbstractCarrier
     */
    public function beforeGetIfActive(
        \Magento\Shipping\Model\CarrierFactory $subject,
        $carrierCode
    )
    {
        return [$this->getSourceCarrierCode($carrierCode)];
    }

    /**
     * Before create if active
     * 
     * @param \Magento\Shipping\Model\CarrierFactory $subject
     * @param array|string $carrierCode
     * @param int|null $storeId
     * @return bool|\Magento\Shipping\Model\Carrier\AbstractCarrier
     */
    public function beforeCreateIfActive(
        \Magento\Shipping\Model\CarrierFactory $subject,
        $carrierCode,
        $storeId = null
    )
    {
        return [$this->getSourceCarrierCode($carrierCode), $storeId];
    }

    /**
     * Get source carrier code
     * 
     * @param array|string $carrierCode
     * @return string
     */
    private function getSourceCarrierCode($carrierCode)
    {
        if (is_array($carrierCode)) {
            $sourceCode = (string) $this->currentSourceProvider->getSourceCode();
            if (!empty($sourceCode)) {
                $carrierCode = !empty($carrierCode[$sourceCode]) ? $carrierCode[$sourceCode] : null;
            } else {
                $carrierCode = current($carrierCode);
            }
        }
        return $carrierCode;
    }
}