<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Controller\Paypal\Express\AbstractExpress;

/**
 * Place order PayPal express controller plugin
 */
class PlaceOrder extends \Ecombricks\Common\Plugin\Plugin
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
     * Around execute
     *
     * @param \Magento\Paypal\Controller\Express\AbstractExpress\PlaceOrder $subject
     * @param callable $proceed
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundExecute(
        \Magento\Paypal\Controller\Express\AbstractExpress\PlaceOrder $subject,
        \Closure $proceed
    )
    {
        $this->setSubject($subject);
        if (!$this->quoteConfig->isSplitOrder()) {
            return $proceed();
        }
        $agreement = $subject->getRequest()->getPost('agreement', []);
        if ($this->invokeSubjectMethod('isValidationRequired') && !$this->getSubjectPropertyValue('agreementsValidator')->isValid(array_keys($agreement))) {
            $exception = new \Magento\Framework\Exception\LocalizedException(
                __('The order wasn\'t placed. First, agree to the terms and conditions, then try placing your order again.')
            );
            $this->getSubjectPropertyValue('messageManager')->addExceptionMessage($exception, $exception->getMessage());
            $this->invokeSubjectMethod('_redirect', '*/*/review');
            return;
        }
        try {
            $this->invokeSubjectMethod('_initCheckout');
            $checkout = $this->getSubjectPropertyValue('_checkout');
            $checkout->place($this->invokeSubjectMethod('_initToken'));
            $orders = (array) $checkout->getOrder();
            $this->updateCheckoutSession($this->getSubjectPropertyValue('_checkoutSession'), $orders);
            $eventManager = $this->getSubjectPropertyValue('_eventManager');
            $quote = $this->invokeSubjectMethod('_getQuote');
            foreach ($orders as $order) {
                $eventManager->dispatch(
                    'paypal_express_place_order_success',
                    [
                        'order' => $order,
                        'quote' => $quote,
                    ]
                );
            }
            $url = $checkout->getRedirectUrl();
            if ($url) {
                $subject->getResponse()->setRedirect($url);
                return;
            }
            $this->invokeSubjectMethod('_getSession')->unsQuoteId();
            $this->invokeSubjectMethod('_initToken', false);
            $this->invokeSubjectMethod('_redirect', 'checkout/onepage/success');
            return;
        } catch (\Magento\Paypal\Model\Api\ProcessableException $exception) {
            $this->invokeSubjectMethod('_processPaypalApiError', $exception);
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $this->processException($exception, $exception->getRawMessage());
        } catch (\Exception $exception) {
            $this->processException($exception, 'We can\'t place the order.');
        }
    }

    /**
     * Update checkout session
     * 
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $orders
     * @return void
     */
    private function updateCheckoutSession(\Magento\Checkout\Model\Session $checkoutSession, array $orders): void
    {
        $quote = $checkoutSession->getQuote();
        $checkoutSession->setLastQuoteId($quote->getId());
        $checkoutSession->setLastSuccessQuoteId($quote->getId());
        $checkoutSession->clearHelperData();
        if (!count($orders)) {
            return;
        }
        $orderIds = [];
        $orderIncrementIds = [];
        $orderStatuses = [];
        foreach ($orders as $order) {
            $orderId = $order->getId();
            $orderIds[] = $orderId;
            $orderIncrementIds[$orderId] = $order->getIncrementId();
            $orderStatuses[$orderId] = $order->getStatus();
        }
        $checkoutSession->setLastOrderIds($orderIds);
        $checkoutSession->setLastRealOrderIds($orderIncrementIds);
        $checkoutSession->setLastOrderStatuses($orderStatuses);
        $lastOrder = end($orders);
        $checkoutSession->setLastOrderId($lastOrder->getId());
        $checkoutSession->setLastRealOrderId($lastOrder->getIncrementId());
        $checkoutSession->setLastOrderStatus($lastOrder->getStatus());
    }
    
    /**
     * Process exception
     *
     * @param \Exception $exception
     * @param string $message
     * @return void
     */
    private function processException(\Exception $exception, string $message): void
    {
        $this->getSubjectPropertyValue('messageManager')->addExceptionMessage($exception, __($message));
        $this->invokeSubjectMethod('_redirect', '*/*/review');
    }
}