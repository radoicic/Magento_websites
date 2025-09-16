<?php
/**
 * Order model interface.
 */

namespace CityBeach\Integration\Api\Data;

use \Magento\Framework\Api\ExtensibleDataInterface;

interface OrderInterface extends ExtensibleDataInterface
{
    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     */
    public function setStoreId($storeId);

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $orderId
     */
    public function setOrderId($orderId);

    /**
     * @return string
     */
    public function getMarketplaceCode();

    /**
     * @param string $marketplaceCode
     */
    public function setMarketplaceCode($marketplaceCode);

    /**
     * @return string
     */
    public function getOrderPrefix();

    /**
     * @param string $orderPrefix
     */
    public function setOrderPrefix($orderPrefix);

    /**
     * @return string
     */
    public function getMarketplaceOrderNumber();

    /**
     * @param string $marketplaceOrderNumber
     */
    public function setMarketplaceOrderNumber($marketplaceOrderNumber);

    /**
     * @return float
     */
    public function getTotalPrice();

    /**
     * @param float $totalPrice
     */
    public function setTotalPrice($totalPrice);

    /**
     * @return float
     */
    public function getTotalTax();

    /**
     * @param float $totalTax
     */
    public function setTotalTax($totalTax);

    /**
     * @return \CityBeach\Integration\Api\Data\ShippingInterface
     */
    public function getShipping();

    /**
     * @param \CityBeach\Integration\Api\Data\ShippingInterface $shipping
     */
    public function setShipping($shipping);

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @param string $currency
     */
    public function setCurrency($currency);

    /**
     * @return string
     */
    public function getPickupStoreId();

    /**
     * @param string $pickupStoreId
     */
    public function setPickupStoreId($pickupStoreId);

    /**
     * @return string
     */
    public function getDispatchStoreId();

    /**
     * @param string $dispatchStoreId
     */
    public function setDispatchStoreId($dispatchStoreId);

    /**
     * @return string
     */
    public function getCustomOrderStatus();

    /**
     * @param string $customOrderStatus
     */
    public function setCustomOrderStatus($customOrderStatus);

  /**
     * @return \CityBeach\Integration\Api\Data\CustomerInterface
     */
    public function getCustomer();

    /**
     * @param \CityBeach\Integration\Api\Data\CustomerInterface $customer
     */
    public function setCustomer($customer);

    /**
     * @return \CityBeach\Integration\Api\Data\AddressInterface
     */
    public function getBillingAddress();

    /**
     * @param \CityBeach\Integration\Api\Data\AddressInterface $billingAddress
     */
    public function setBillingAddress($billingAddress);

    /**
     * @return \CityBeach\Integration\Api\Data\AddressInterface
     */
    public function getShippingAddress();

    /**
     * @param \CityBeach\Integration\Api\Data\AddressInterface $shippingAddress
     */
    public function setShippingAddress($shippingAddress);

    /**
     * @return \CityBeach\Integration\Api\Data\ProductInterface[]
     */
    public function getProducts();

    /**
     * @param \CityBeach\Integration\Api\Data\ProductInterface $products[]
     */
    public function setProducts($products);

    /**
     * @return \CityBeach\Integration\Api\Data\PaymentInterface
     */
    public function getPayment();

    /**
     * @param \CityBeach\Integration\Api\Data\PaymentInterface $payment
     */
    public function setPayment($payment);

    /**
     * @return string
     */
    public function getDeliveryInstructions();

    /**
     * @param string $deliveryInstructions
     */
    public function setDeliveryInstructions($deliveryInstructions);

    /**
     * @throws \Exception if validation of these object fails.
     */
    public function validate();
}
