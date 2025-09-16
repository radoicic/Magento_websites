<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Paypal\Express;

/**
 * PayPal express checkout model plugin
 */
class Checkout extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Quote configuration
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config
     */
    private $quoteConfig;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config $quoteConfig
     * @retun void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config $quoteConfig
    )
    {
        parent::__construct($wrapperFactory);
        $this->quoteConfig = $quoteConfig;
    }

    /**
     * Around place
     *
     * @param \Magento\Paypal\Model\Express\Checkout $subject
     * @param callable $proceed
     * @param string $token
     * @param string|null $shippingMethodCode
     * @return void
     */
    public function aroundPlace(
        \Magento\Paypal\Model\Express\Checkout $subject,
        \Closure $proceed,
        $token,
        $shippingMethodCode = null
    )
    {
        $this->setSubject($subject);
        if (!$this->quoteConfig->isSplitOrder()) {
            return $proceed($token, $shippingMethodCode);
        }
        if ($shippingMethodCode) {
            $subject->updateShippingMethod($shippingMethodCode);
        }
        if ($subject->getCheckoutMethod() == \Magento\Checkout\Model\Type\Onepage::METHOD_GUEST) {
            $this->invokeSubjectMethod('prepareGuestQuote');
        }
        $this->invokeSubjectMethod('ignoreAddressValidation');
        $quote = $this->getSubjectPropertyValue('_quote');
        $quote->collectTotals();
        $orders = $this->getSubjectPropertyValue('quoteManagement')->submit($quote);
        if (empty($orders)) {
            return $this;
        }
        $order = current($orders);
        if ($order->getPayment()->getAdditionalInformation(self::PAYMENT_INFO_TRANSPORT_REDIRECT)) {
            $this->setSubjectPropertyValue('_redirectUrl', $this->getSubjectPropertyValue('_config')->getExpressCheckoutCompleteUrl($token));
        }
        foreach ($orders as $order) {
            $this->sendOrderEmail($order);
        }
        $this->setSubjectPropertyValue('_order', $orders);
    }

    /**
     * Send order email
     * 
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return void
     */
    private function sendOrderEmail(\Magento\Sales\Api\Data\OrderInterface $order): void
    {
        if (!in_array($order->getState(), [
            \Magento\Sales\Model\Order::STATE_PROCESSING,
            \Magento\Sales\Model\Order::STATE_COMPLETE,
            \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW,
        ])) {
            return;
        }
        if ($order->getEmailSent()) {
            return;
        }
        try {
            $this->getSubjectPropertyValue('orderSender')->send($order);
        } catch (\Exception $exception) {
            $this->getSubjectPropertyValue('_logger')->critical($exception);
        }
        $this->getSubjectPropertyValue('_checkoutSession')->start();
    }
}