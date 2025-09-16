<?php
/**
 * Product model interface.
 */

namespace CityBeach\Integration\Api\Data;

use \Magento\Framework\Api\ExtensibleDataInterface;

interface ProductInterface extends ExtensibleDataInterface
{
    /**
     * @return string
     */
    public function getSku();

    /**
     * @param string $sku
     */
    public function setSku($sku);

    /**
     * @return string
     */
    public function getVariantCode();

    /**
     * @param string $variantCode
     */
    public function setVariantCode($variantCode);

    /**
     * @return string
     */
    public function getVariantName();

    /**
     * @param string $variantName
     */
    public function setVariantName($variantName);

    /**
     * @return string
     */
    public function getExternalId();

    /**
     * @param string $externalId
     */
    public function setExternalId($externalId);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity);

    /**
     * @return float
     */
    public function getUnitPrice();

    /**
     * @param float $unitPrice
     */
    public function setUnitPrice($unitPrice);

    /**
     * @throws \Exception if validation of these object fails.
     */
    public function validate();
}
