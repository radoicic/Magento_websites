<?php
/**
 * Shipping model interface.
 */

namespace CityBeach\Integration\Api\Data;

use \Magento\Framework\Api\ExtensibleDataInterface;

interface ShippingInterface extends ExtensibleDataInterface
{
    /**
     * @return string
     */
    public function getCarrier();

    /**
     * @param string $carrier
     */
    public function setCarrier($carrier);

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @param string $method
     */
    public function setMethod($method);

    /**
     * @return string
     */
    public function getCarrierTitle();

    /**
     * @param string $carrierTitle
     */
    public function setCarrierTitle($carrierTitle);

    /**
     * @return string
     */
    public function getMethodTitle();

    /**
     * @param string $methodTitle
     */
    public function setMethodTitle($methodTitle);

    /**
     * @return float
     */
    public function getPrice();

    /**
     * @param float $tax
     */
    public function setTax($tax);

    /**
     * @return float
     */
    public function getTax();

    /**
     * @param float $price
     */
    public function setPrice($price);

    /**
     * @throws \Exception if validation of these object fails.
     */
    public function validate();
}
