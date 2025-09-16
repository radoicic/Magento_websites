<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Controller\Sales\Adminhtml\Order\Create;

/**
 * Create order save controller plugin
 */
class Save extends \Ecombricks\Common\Plugin\Plugin
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
     * @return void
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
     * @param \Magento\Sales\Controller\Adminhtml\Order\Create\Save $subject
     * @param \Closure $proceed
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function aroundExecute(
        \Magento\Sales\Controller\Adminhtml\Order\Create\Save $subject,
        \Closure $proceed
    )
    {
        $this->setSubject($subject);
        if (!$this->quoteConfig->isSplitOrder()) {
            return $proceed();
        }
        $path = 'sales/*/';
        $pathParams = [];
        $session = $this->invokeSubjectMethod('_getSession');
        $orderCreate = $this->invokeSubjectMethod('_getOrderCreateModel');
        $messageManager = $this->getSubjectPropertyValue('messageManager');
        $request = $subject->getRequest();
        try {
            if (!$this->authorize()) {
                return $this->getSubjectPropertyValue('resultForwardFactory')->create()->forward('denied');
            }
            $orderCreate->getQuote()->setCustomerId($session->getCustomerId());
            $this->invokeSubjectMethod('_processActionData', 'save');
            $this->preparePayment();
            $orderCreate->setIsValidate(true);
            $orderCreate->importPostData($request->getPost('order'));
            $orders = $orderCreate->createOrder();
            $session->clearStorage();
            $messageManager->addSuccessMessage(__('You created the orders.'));
            if (count($orders) == 1 && $this->getSubjectPropertyValue('_authorization')->isAllowed('Magento_Sales::actions_view')) {
                $pathParams = ['order_id' => current($orders)->getId()];
                $path = 'sales/order/view';
            } else {
                $path = 'sales/order/index';
            }
        } catch (\Magento\Framework\Exception\PaymentException $exception) {
            $orderCreate->saveQuote();
            $this->addExceptionMessage($exception);
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $session->setCustomerId((int) $session->getQuote()->getCustomerId());
            $this->addExceptionMessage($exception);
        } catch (\Exception $exception) {
            $messageManager->addExceptionMessage($exception, __('Orders saving error: %1', $exception->getMessage()));
        }
        return $this->getSubjectPropertyValue('resultRedirectFactory')->create()->setPath($path, $pathParams);
    }

    /**
     * Authorize
     * 
     * @return bool
     */
    private function authorize(): bool
    {
        $session = $this->invokeSubjectMethod('_getSession');
        return $this->getSubjectPropertyValue('_authorization')->isAllowed('Magento_Customer::manage') || 
            $session->getCustomerId() || 
            $session->getQuote()->getCustomerIsGuest();
    }
    
    /**
     * Get payment method checks
     * 
     * @return array
     */
    private function getPaymentMethodChecks(): array
    {
        return [
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_INTERNAL,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_COUNTRY,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_CURRENCY,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_ZERO_TOTAL,
        ];
    }
    
    /**
     * Prepare payment
     * 
     * @return void
     */
    private function preparePayment(): void
    {
        $subject = $this->getSubject();
        $paymentData = $subject->getRequest()->getPost('payment');
        if (empty($paymentData)) {
            return;
        }
        $paymentData['checks'] = $this->getPaymentMethodChecks();
        $orderCreate = $this->invokeSubjectMethod('_getOrderCreateModel');
        $orderCreate->setPaymentData($paymentData);
        $orderCreate->getQuote()->getPayment()->addData($paymentData);
    }
    
    /**
     * Add exception message
     * 
     * @param \Exception $exception
     * @return void
     */
    private function addExceptionMessage(\Exception $exception): void
    {
        $message = $exception->getMessage();
        if (empty($message)) {
            return;
        }
        $this->getSubjectPropertyValue('messageManager')->addErrorMessage($message);
    }
}