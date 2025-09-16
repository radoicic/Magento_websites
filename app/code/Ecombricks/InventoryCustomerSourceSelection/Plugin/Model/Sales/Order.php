<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Sales;

/**
 * Order model plugin
 */
class Order extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Shipping method full code
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Data\Quote\ShippingMethodFullCode
     */
    private $shippingMethodFullCode;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Data\Quote\ShippingMethodFullCode $shippingMethodFullCode
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Data\Quote\ShippingMethodFullCode $shippingMethodFullCode
    )
    {
        parent::__construct($wrapperFactory);
        $this->shippingMethodFullCode = $shippingMethodFullCode;
    }
    
    /**
     * After after load
     * 
     * @param \Magento\Sales\Model\Order $subject
     * @param \Magento\Sales\Model\Order $result
     * @return \Magento\Sales\Model\Order
     */
    public function afterAfterLoad(\Magento\Sales\Model\Order $subject, $result)
    {
        $this->setSubject($subject);
        $this->getOrderWrapper()->afterLoad();
        return $result;
    }

    /**
     * After after save
     * 
     * @param \Magento\Sales\Model\Order $subject
     * @param \Magento\Sales\Model\Order $result
     * @return \Magento\Sales\Model\Order
     */
    public function afterAfterSave(\Magento\Sales\Model\Order $subject, $result)
    {
        $this->setSubject($subject);
        $this->getOrderWrapper()->afterSave();
        return $result;
    }

    /**
     * Around get shipping method
     * 
     * @param \Magento\Sales\Model\Order $subject
     * @param \Closure $proceed
     * @param bool $asObject
     * @return array|\Magento\Framework\DataObject
     */
    public function aroundGetShippingMethod(
        \Magento\Sales\Model\Order $subject,
        \Closure $proceed,
        $asObject = false
    )
    {
        $this->setSubject($subject);
        $shippingMethods = $this->getOrderWrapper()->getShippingMethods();
        if (!$asObject) {
            return $shippingMethods;
        } else {
            $carrierCodes = $methodCodes = [];
            foreach ($shippingMethods as $sourceCode => $shippingMethod) {
                $shippingMethodPieces = $this->shippingMethodFullCode->parse($shippingMethod);
                $carrierCodes[$sourceCode] = $shippingMethodPieces[0];
                $methodCodes[$sourceCode] = !empty($shippingMethodPieces[1]) ? $shippingMethodPieces[1] : '';
            }
            return new \Magento\Framework\DataObject(['carrier_code' => $carrierCodes, 'method' => $methodCodes]);
        }
    }
    
    /**
     * Around set data
     * 
     * @param \Magento\Sales\Model\Order $subject
     * @param \Closure $proceed
     * @param array|string $key
     * @param null $value
     * @return \Magento\Sales\Model\Order
     */
    public function aroundSetData(
        \Magento\Sales\Model\Order $subject,
        \Closure $proceed,
        $key,
        $value = null
    )
    {
        $this->setSubject($subject);
        if (is_array($key) && array_key_exists('shipping_method', $key)) {
            $proceed($key, $value);
            $this->getOrderWrapper()->setShippingMethods(!empty($key['shipping_method'] && is_array($key['shipping_method'])) ? $key['shipping_method'] : []);
            return $subject;
        } else if (is_string($key) && $key === 'shipping_method') {
            $this->getOrderWrapper()->setShippingMethods(!empty($value) && is_array($value) ? $value : []);
            return $subject;
        }
        return $proceed($key, $value);
    }

    /**
     * Create
     * 
     * @return \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order
     */
    private function getOrderWrapper(): \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order
    {
        return $this->wrapperFactory->create(
            $this->getSubject(),
            \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order::class
        );
    }
}