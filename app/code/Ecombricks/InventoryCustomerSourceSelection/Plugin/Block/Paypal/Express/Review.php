<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Block\Paypal\Express;

/**
 * PayPal express review block plugin
 */
class Review extends \Ecombricks\Common\Plugin\View\Framework\Element\AbstractBlock
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
     * Around get current shipping rate
     *
     * @param \Magento\Paypal\Block\Express\Review $subject
     * @param callable $proceed
     * @return \Magento\Quote\Model\Quote\Address\Rate
     */
    public function aroundGetCurrentShippingRate(
        \Magento\Paypal\Block\Express\Review $subject,
        \Closure $proceed
    )
    {
        $this->setSubject($subject);
        return $this->quoteAddressWrapperFactory->create($subject->getShippingAddress())->getCurrentShippingRates();
    }

    /**
     * Before to HTML
     *
     * @return $this
     */
    protected function beforeToHtml()
    {
        $subject = $this->getSubject();
        $quote = $this->getSubjectPropertyValue('_quote');
        $controllerPath = $this->getSubjectPropertyValue('_controllerPath');
        $payment = $quote->getPayment();
        $subject->setPaymentMethodTitle($payment->getMethodInstance()->getTitle());
        $subject->setShippingRateRequired(true);
        if ($quote->getIsVirtual()) {
            $subject->setShippingRateRequired(false);
        } else {
            $this->setSubjectPropertyValue('_address', $quote->getShippingAddress());
            $canEditShippingAddress = $quote->getMayEditShippingAddress() && 
                $payment->getAdditionalInformation(\Magento\Paypal\Model\Express\Checkout::PAYMENT_INFO_BUTTON) == 1;
            $subject->setShippingMethodSubmitUrl($subject->getUrl($controllerPath.'/saveShippingMethod', ['_secure' => true]));
            $subject->setCanEditShippingAddress($canEditShippingAddress);
            $subject->setCanEditShippingMethod($quote->getMayEditShippingMethod());
        }
        $subject->setEditUrl($subject->getUrl($controllerPath.'/edit'));
        $subject->setPlaceOrderUrl($subject->getUrl($controllerPath.'/placeOrder', ['_secure' => true]));
        return $this;
    }
}