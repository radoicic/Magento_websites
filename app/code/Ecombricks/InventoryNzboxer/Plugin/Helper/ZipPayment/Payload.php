<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryNzboxer\Plugin\Helper\ZipPayment;

class Payload extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
    )
    {
        parent::__construct($wrapperFactory);
    }
    
    /**
     * @param \Zip\ZipPayment\Helper\Payload $subject
     * @param \Closure $proceed
     * @return \Zip\ZipPayment\MerchantApi\Lib\Model\OrderShipping
     */
    public function aroundGetShippingDetails(
        \Zip\ZipPayment\Helper\Payload $subject,
        \Closure $proceed
    )
    {
        $this->setSubject($subject);
        $shipping = new \Zip\ZipPayment\MerchantApi\Lib\Model\OrderShipping;
        if ($this->getSubjectPropertyValue('_isVirtual')) {
            $shipping->setPickup(true);
            return $shipping;
        }
        if ($subject->getQuote()) {
            $shipping_address = $subject->getQuote()->getShippingAddress();
        } elseif ($subject->getOrder()) {
            $shipping_address = $subject->getOrder()->getShippingAddress();
            if ($shipping_address) {
                $shipping_method = $shipping_address->getShippingMethod();
                if ($shipping_method) {
                    $tracking = new \Zip\ZipPayment\MerchantApi\Lib\Model\OrderShippingTracking;
                    $tracking->setNumber($this->invokeSubjectMethod('getTrackingNumbers'));
                    $tracking->setCarrier(current($shipping_method));
                    $shipping->setTracking($tracking);
                }
            }
        }
        if ($shipping_address) {
            if ($address = $this->invokeSubjectMethod('_getAddress', $shipping_address)) {
                $shipping->setPickup(false)->setAddress($address);
            }
        }
        return $shipping;
    }
}