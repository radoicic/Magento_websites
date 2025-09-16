<?php

namespace CityBeach\Integration\Model\Order;

use CityBeach\Integration\Api\Data\ShippingInterface;

class Shipping implements ShippingInterface
{
    /**
     * @var string $carrier
     */
    private $carrier;

    /**
     * @var string $carrierTitle
     */
    private $carrierTitle;

    /**
     * @var string $method
     */
    private $method;

    /**
     * @var string $methodTitle
     */
    private $methodTitle;

    /**
     * @var float $price
     */
    private $price;

    /**
     * @var float $tax
     */
    private $tax;

    /**
     * @return string
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * @param string $carrier
     */
    public function setCarrier($carrier)
    {
        $this->carrier = $carrier;
    }


    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getCarrierTitle()
    {
        return $this->carrierTitle;
    }

    /**
     * @param string $carrierTitle
     */
    public function setCarrierTitle($carrierTitle)
    {
        $this->carrierTitle = $carrierTitle;
    }

    /**
     * @return string
     */
    public function getMethodTitle()
    {
        return $this->methodTitle;
    }

    /**
     * @param string $methodTitle
     */
    public function setMethodTitle($methodTitle)
    {
        $this->methodTitle = $methodTitle;
    }


    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @param float $tax
     */
    public function setTax($tax)
    {
        $this->tax = $tax;
    }

    public function validate()
    {
        if (empty($this->method) ||
            !isset($this->price)
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Missing required property on shipping details')
            );
        }
    }
}
