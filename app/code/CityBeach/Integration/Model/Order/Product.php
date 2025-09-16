<?php

namespace CityBeach\Integration\Model\Order;

use CityBeach\Integration\Api\Data\ProductInterface;

class Product implements ProductInterface
{

    /**
     * @var string $sku
     */
    private $sku;

    /**
     * @var string $variantCode
     */
    private $variantCode;

    /**
     * @var string $variantName
     */
    private $variantName;

    /**
     * @var string $externalId
     */
    private $externalId;

    /**
     * @var int $quantity
     */
    private $quantity;

    /**
     * @var float $unitPrice
     */
    private $unitPrice;

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    /**
     * @return string
     */
    public function getVariantCode()
    {
        return $this->variantCode;
    }

    /**
     * @param string $variantCode
     */
    public function setVariantCode($variantCode)
    {
        $this->variantCode = $variantCode;
    }

    /**
     * @return string
     */
    public function getVariantName()
    {
        return $this->variantName;
    }

    /**
     * @param string $variantName
     */
    public function setVariantName($variantName)
    {
        $this->variantName = $variantName;
    }

    /**
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param string $externalId
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return float
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @param float $unitPrice
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;
    }

    public function validate()
    {
        if (empty($this->sku) ||
            empty($this->quantity) ||
            empty($this->unitPrice)
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Missing required property on product'));
        }
    }
}
