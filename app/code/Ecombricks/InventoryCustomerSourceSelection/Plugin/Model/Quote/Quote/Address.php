<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Quote\Quote;

/**
 * Quote address plugin
 */
class Address extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Quote address wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory
     */
    private $quoteAddressWrapperFactory;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory
    )
    {
        parent::__construct($wrapperFactory);
        $this->quoteAddressWrapperFactory = $quoteAddressWrapperFactory;
    }
    
    /**
     * After after load
     * 
     * @param \Magento\Quote\Model\Quote\Address $subject
     * @param \Magento\Quote\Model\Quote\Address $result
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function afterAfterLoad(\Magento\Quote\Model\Quote\Address $subject, $result)
    {
        $this->setSubject($subject);
        $this->quoteAddressWrapperFactory->create($subject)->afterLoad();
        return $result;
    }

    /**
     * After after save
     * 
     * @param \Magento\Quote\Model\Quote\Address $subject
     * @param \Magento\Quote\Model\Quote\Address $result
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function afterAfterSave(\Magento\Quote\Model\Quote\Address $subject, $result)
    {
        $this->setSubject($subject);
        $this->quoteAddressWrapperFactory->create($subject)->afterSave();
        return $result;
    }
    
    /**
     * Around request shipping rates
     * 
     * @param \Magento\Quote\Model\Quote\Address $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return bool
     */
    public function aroundRequestShippingRates(
        \Magento\Quote\Model\Quote\Address $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item = null
    )
    {
        $this->setSubject($subject);
        return $this->quoteAddressWrapperFactory->create($subject)->requestShippingRates($item);
    }
    
    /**
     * Around get grouped all shipping rates
     * 
     * @param \Magento\Quote\Model\Quote\Address $subject
     * @param \Closure $proceed
     * @return array
     */
    public function aroundGetGroupedAllShippingRates(
        \Magento\Quote\Model\Quote\Address $subject,
        \Closure $proceed
    )
    {
        $this->setSubject($subject);
        return $this->quoteAddressWrapperFactory->create($subject)->getGroupedAllShippingRates();
    }
    
    /**
     * Around get shipping rate by code
     * 
     * @param \Magento\Quote\Model\Quote\Address $subject
     * @param \Closure $proceed
     * @param string $code
     * @return \Magento\Quote\Model\Quote\Address\Rate[]
     */
    public function aroundGetShippingRateByCode(
        \Magento\Quote\Model\Quote\Address $subject,
        \Closure $proceed,
        $code
    )
    {
        $this->setSubject($subject);
        return $this->quoteAddressWrapperFactory->create($subject)->getShippingRatesByCodes($code);
    }
    
    /**
     * Around get data
     * 
     * @param \Magento\Quote\Model\Quote\Address $subject
     * @param \Closure $proceed
     * @param string $key
     * @param string|int $index
     * @return mixed
     */
    public function aroundGetData(
        \Magento\Quote\Model\Quote\Address $subject,
        \Closure $proceed,
        $key = '',
        $index = null
    )
    {
        $this->setSubject($subject);
        if ($key === 'shipping_method') {
            return $this->quoteAddressWrapperFactory->create($subject)->getShippingMethods();
        }
        return $proceed($key, $index);
    }
    
    /**
     * Around set data
     * 
     * @param \Magento\Quote\Model\Quote\Address $subject
     * @param \Closure $proceed
     * @param array|string $key
     * @param null $value
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function aroundSetData(
        \Magento\Quote\Model\Quote\Address $subject,
        \Closure $proceed,
        $key,
        $value = null
    )
    {
        $this->setSubject($subject);
        if (is_array($key) && array_key_exists('shipping_method', $key)) {
            $proceed($key, $value);
            $this->quoteAddressWrapperFactory->create($subject)->setShippingMethods(!empty($key['shipping_method'] && is_array($key['shipping_method'])) ? $key['shipping_method'] : []);
            return $subject;
        } else if (is_string($key) && $key === 'shipping_method') {
            $this->quoteAddressWrapperFactory->create($subject)->setShippingMethods(!empty($value) && is_array($value) ? $value : []);
            return $subject;
        }
        return $proceed($key, $value);
    }
}