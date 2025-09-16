<?php

namespace CityBeach\Integration\Model\Order;

use CityBeach\Integration\Api\Data\OrderInterface;
use CityBeach\Integration\Api\Data\AddressInterface;
use CityBeach\Integration\Api\Data\CustomerInterface;
use CityBeach\Integration\Api\Data\ShippingInterface;
use CityBeach\Integration\Api\Data\PaymentInterface;
use CityBeach\Integration\Api\Data\ProductInterface;

class Order implements OrderInterface
{
    /**
     * @var int $storeId
     */
    private $storeId;

    /**
     * @var int $orderId
     */
    private $orderId;

    /**
     * @var string $marketplaceCode
     */
    private $marketplaceCode;

    /**
     * @var string $orderPrefix
     */
    private $orderPrefix;

    /**
     * @var string $marketplaceOrderNumber
     */
    private $marketplaceOrderNumber;

    /**
     * @var float $totalPrice
     */
    private $totalPrice;

    /**
     * @var float $totalTax
     */
    private $totalTax;

    /**
     * @var string $currency
     */
    private $currency;

    /**
     * @var string $pickupStoreId
     */
    private $pickupStoreId;

    /**
     * @var string $dispatchStoreId
     */
    private $dispatchStoreId;

    /**
     * @var string $customOrderStatus
     */
    private $customOrderStatus;

    /**
     * @var CustomerInterface $customer
     */
    private $customer;

    /**
     * @var ShippingInterface $shipping
     */
    private $shipping;

    /**
     * @var AddressInterface $billingAddress
     */
    private $billingAddress;

    /**
     * @var AddressInterface $shippingAddress
     */
    private $shippingAddress;

    /**
     * @var ProductInterface $products []
     */
    private $products;

    /**
     * @var PaymentInterface $payment
     */
    private $payment;

    /**
     * @var string $deliveryInstructions
     */
    private $deliveryInstructions;

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @param int $storeId
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return string
     */
    public function getOrderPrefix()
    {
        return $this->orderPrefix;
    }

    /**
     * @param string $orderPrefix
     */
    public function setOrderPrefix($orderPrefix)
    {
        $this->orderPrefix = $orderPrefix;
    }

    /**
     * @return string
     */
    public function getMarketplaceCode()
    {
        return $this->marketplaceCode;
    }

    /**
     * @param string $marketplaceCode
     */
    public function setMarketplaceCode($marketplaceCode)
    {
        $this->marketplaceCode = $marketplaceCode;
    }

    /**
     * @return string
     */
    public function getMarketplaceOrderNumber()
    {
        return $this->marketplaceOrderNumber;
    }

    /**
     * @param string $marketplaceOrderNumber
     */
    public function setMarketplaceOrderNumber($marketplaceOrderNumber)
    {
        $this->marketplaceOrderNumber = $marketplaceOrderNumber;
    }

    /**
     * @return float
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @param float $totalPrice
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;
    }

    /**
     * @return float
     */
    public function getTotalTax()
    {
        return $this->totalTax;
    }

    /**
     * @param float $totalTax
     */
    public function setTotalTax($totalTax)
    {
        $this->totalTax = $totalTax;
    }

    /**
     * @return ShippingInterface
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /**
     * @param ShippingInterface $shipping
     */
    public function setShipping($shipping)
    {
        $this->shipping = $shipping;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getPickupStoreId()
    {
        return $this->pickupStoreId;
    }

    /**
     * @param string $pickupStoreId
     */
    public function setPickupStoreId($pickupStoreId)
    {
        $this->pickupStoreId = $pickupStoreId;
    }

    /**
     * @return string
     */
    public function getDispatchStoreId()
    {
        return $this->dispatchStoreId;
    }

    /**
     * @param string $dispatchStoreId
     */
    public function setDispatchStoreId($dispatchStoreId)
    {
        $this->dispatchStoreId = $dispatchStoreId;
    }

    /**
     * @return string
     */
    public function getCustomOrderStatus()
    {
        return $this->customOrderStatus;
    }

    /**
     * @param string $customOrderStatus
     */
    public function setCustomOrderStatus($customOrderStatus)
    {
        $this->customOrderStatus = $customOrderStatus;
    }

    /**
     * @return CustomerInterface
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param CustomerInterface $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return AddressInterface
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * @param AddressInterface $billingAddress
     */
    public function setBillingAddress($billingAddress)
    {
        $this->billingAddress = $billingAddress;
    }

    /**
     * @return AddressInterface
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * @param AddressInterface $shippingAddress
     */
    public function setShippingAddress($shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
    }

    /**
     * @return ProductInterface
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param ProductInterface $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }

    /**
     * @return PaymentInterface
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param PaymentInterface $payment
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return string
     */
    public function getDeliveryInstructions()
    {
        return $this->deliveryInstructions;
    }

    /**
     * @param string $deliveryInstructions
     */
    public function setDeliveryInstructions($deliveryInstructions)
    {
        $this->deliveryInstructions = $deliveryInstructions;
    }


    public function validate()
    {
        if (empty($this->storeId) ||
            empty($this->orderId) ||
            empty($this->marketplaceCode) ||
            empty($this->marketplaceOrderNumber) ||
            !isset($this->totalPrice) ||
            !isset($this->totalTax) ||
            empty($this->shipping) ||
            empty($this->currency) ||
            empty($this->customer) ||
            empty($this->billingAddress) ||
            empty($this->shippingAddress) ||
            empty($this->products) ||
            empty($this->payment)
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Missing required property on order'));
        }

        $this->customer->validate();
        $this->billingAddress->validate();
        $this->shippingAddress->validate();
        $this->shipping->validate();
        foreach ($this->products as $product) {
            $product->validate();
        }
        $this->payment->validate();
    }
}
