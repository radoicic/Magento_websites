<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Quote;

/**
 * Quote management plugin
 */
class QuoteManagement extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Quote configuration
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config
     */
    private $quoteConfig;
    
    /**
     * Quote address wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory
     */
    private $quoteAddressWrapperFactory;

    /**
     * Quote wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\QuoteFactory 
     */
    private $quoteWrapperFactory;

    /**
     * Customer address repository
     * 
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $customerAddressRepository;
    
    /**
     * Request
     * 
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;
    
    /**
     * Remote address
     * 
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;
    
    /**
     * Quote validator
     * 
     * @var \Magento\Quote\Model\QuoteValidator
     */
    private $quoteValidator;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config $quoteConfig
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\QuoteFactory $quoteWrapperFactory
     * @param \Magento\Customer\Api\AddressRepositoryInterface $customerAddressRepository
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Magento\Quote\Model\QuoteValidator $quoteValidator
     * @retun void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config $quoteConfig,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\QuoteFactory $quoteWrapperFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $customerAddressRepository,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Quote\Model\QuoteValidator $quoteValidator
    )
    {
        parent::__construct($wrapperFactory);
        $this->quoteConfig = $quoteConfig;
        $this->quoteAddressWrapperFactory = $quoteAddressWrapperFactory;
        $this->quoteWrapperFactory = $quoteWrapperFactory;
        $this->customerAddressRepository = $customerAddressRepository;
        $this->request = $request;
        $this->remoteAddress = $remoteAddress;
        $this->quoteValidator = $quoteValidator;
    }

    /**
     * Around submit
     * 
     * @param \Magento\Quote\Model\QuoteManagement $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote $quote
     * @param array $orderData
     * @return mixed
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundSubmit(
        \Magento\Quote\Model\QuoteManagement $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote $quote,
        $orderData = []
    )
    {
        $this->setSubject($subject);
        if (!$this->quoteConfig->isSplitOrder()) {
            return $proceed($quote, $orderData);
        }
        if (!$quote->getAllVisibleItems()) {
            $quote->setIsActive(false);
            return [];
        }
        return $this->submitQuote($quote, $orderData);
    }
    
    /**
     * Around place order
     * 
     * @param \Magento\Quote\Model\QuoteManagement $subject
     * @param \Closure $proceed
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface|null $paymentMethod
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return mixed
     */
    public function aroundPlaceOrder(
        \Magento\Quote\Model\QuoteManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod = null
    )
    {
        $this->setSubject($subject);
        if (!$this->quoteConfig->isSplitOrder()) {
            return $proceed($cartId, $paymentMethod);
        }
        $quoteRepository = $this->getSubjectPropertyValue('quoteRepository');
        $eventManager = $this->getSubjectPropertyValue('eventManager');
        $quote = $quoteRepository->getActive($cartId);
        $this->prepareQuotePayment($quote, $paymentMethod);
        $this->prepareQuoteCustomer($quote);
        $this->prepareQuote($quote);
        $eventManager->dispatch(
            'checkout_submit_before', [
                'quote' => $quote,
            ]
        );
        $orders = $subject->submit($quote);
        if (!count($orders)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('A server error stopped your orders from being placed. Please try to place your orders again.'));
        }
        $this->updateCheckoutSession($quote, $orders);
        $orderIds = [];
        foreach ($orders as $order) {
            $orderIds[] = $order->getId();
            $eventManager->dispatch(
                'checkout_submit_all_after',
                [
                    'order' => $order,
                    'quote' => $quote,
                ]
            );
        }
        return $orderIds;
    }

    /**
     * Submit quote
     * 
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param array $orderData
     * @return array
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function submitQuote(\Magento\Quote\Api\Data\CartInterface $quote, array $orderData = []): array
    {
        $placedOrders = [];
        $eventManager = $this->getSubjectPropertyValue('eventManager');
        $orderManagement = $this->getSubjectPropertyValue('orderManagement');
        $quoteRepository = $this->getSubjectPropertyValue('quoteRepository');
        $this->quoteValidator->validateBeforeSubmit($quote);
        $this->prepareQuoteAddressesForLoggedInCustomer($quote);
        $quoteWrapper = $this->quoteWrapperFactory->create($quote);
        foreach ($quoteWrapper->getSourceCodes() as $sourceCode) {
            $order = $this->createOrder(
                $quote,
                $quoteWrapper->createSourceClone($sourceCode),
                !empty($orderData[$sourceCode]) ? $orderData[$sourceCode] : []
            );
            $eventManager->dispatch(
                'sales_model_service_quote_submit_before',
                [
                    'order' => $order,
                    'quote' => $quote,
                ]
            );
            try {
                $placedOrder = $orderManagement->place($order);
                $eventManager->dispatch(
                    'sales_model_service_quote_submit_success',
                    [
                        'order' => $placedOrder,
                        'quote' => $quote,
                    ]
                );
                $placedOrders[$sourceCode] = $placedOrder;
            } catch (\Exception $exception) {
                $this->invokeSubjectMethod('rollbackAddresses', $quote, $order, $exception);
                throw $exception;
            }
        }
        if (!count($placedOrders)) {
            return $placedOrders;
        }
        $quote->setIsActive(false);
        $quoteRepository->save($quote);
        return $placedOrders;
    }
    
    /**
     * Create order
     * 
     * @param \Magento\Quote\Api\Data\CartInterface $origQuote
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param array $orderData
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    protected function createOrder(
        \Magento\Quote\Api\Data\CartInterface $origQuote,
        \Magento\Quote\Api\Data\CartInterface $quote,
        array $orderData = []
    ): \Magento\Sales\Api\Data\OrderInterface
    {
        $orderFactory = $this->getSubjectPropertyValue('orderFactory');
        $order = $orderFactory->create();
        $origQuote->setReservedOrderId(null);
        if (!empty($orderData['increment_id'])) {
            $origQuote->setReservedOrderId($orderData['increment_id']);
        }
        $origQuote->reserveOrderId();
        $orderAddresses = [];
        $quoteBillingAddress = $quote->getBillingAddress();
        if (!$quote->isVirtual()) {
            $quoteShippingAddress = $quote->getShippingAddress();
            $this->copyQuoteAddressToOrder($quoteShippingAddress, $order, $orderData);
            $orderShippingAddress = $this->convertQuoteAddressToOrderAddress($quoteShippingAddress);
            $orderShippingAddress->setData('quote_address_id', $quoteShippingAddress->getId());
            $orderAddresses[] = $orderShippingAddress;
            $order->setShippingAddress($orderShippingAddress);
            $order->setShippingMethod($quoteShippingAddress->getShippingMethod());
            $quoteShippingAddressWrapper = $this->quoteAddressWrapperFactory->create($quoteShippingAddress);
            $quoteShippingAddressWrapper->generateShippingDescription();
            $order->setShippingDescription($quoteShippingAddress->getShippingDescription());
        } else {
            $this->copyQuoteAddressToOrder($quoteBillingAddress, $order, $orderData);
        }
        $orderBillingAddress = $this->convertQuoteAddressToOrderAddress($quoteBillingAddress);
        $orderBillingAddress->setData('quote_address_id', $quoteBillingAddress->getId());
        $orderAddresses[] = $orderBillingAddress;
        $order->setBillingAddress($orderBillingAddress);
        $order->setAddresses($orderAddresses);
        $order->setPayment($this->getSubjectPropertyValue('quotePaymentToOrderPayment')->convert($quote->getPayment()));
        $order->setItems($this->createOrderItems($quote));
        if ($quote->getCustomer()) {
            $order->setCustomerId($quote->getCustomer()->getId());
        }
        $order->setQuoteId($quote->getId());
        $order->setCustomerEmail($quote->getCustomerEmail());
        $order->setCustomerFirstname($quote->getCustomerFirstname());
        $order->setCustomerMiddlename($quote->getCustomerMiddlename());
        $order->setCustomerLastname($quote->getCustomerLastname());
        $order->setIncrementId($origQuote->getReservedOrderId());
        return $order;
    }

    /**
     * Prepare quote addresses for logged in customer
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return void
     */
    protected function prepareQuoteAddressesForLoggedInCustomer(\Magento\Quote\Api\Data\CartInterface $quote): void
    {
        if ($quote->getCustomerIsGuest()) {
            return;
        }
        $customerManagement = $this->getSubjectPropertyValue('customerManagement');
        if (!$quote->getCustomerId()) {
            $customerManagement->populateCustomerInfo($quote);
            return;
        }
        $customerRepository = $this->getSubjectPropertyValue('customerRepository');
        $quoteBillingAddress = $quote->getBillingAddress();
        $quoteShippingAddress = $quote->isVirtual() ? null : $quote->getShippingAddress();
        $customer = $customerRepository->getById($quote->getCustomerId());
        $hasDefaultBilling = (bool) $customer->getDefaultBilling();
        $hasDefaultShipping = (bool) $customer->getDefaultShipping();
        if (
            $quoteShippingAddress && 
            !$quoteShippingAddress->getSameAsBilling() && 
            (!$quoteShippingAddress->getCustomerId() || $quoteShippingAddress->getSaveInAddressBook())
        ) {
            
            if ($quoteShippingAddress->getQuoteId()) {
                $customerShippingAddress = $quoteShippingAddress->exportCustomerAddress();
            } else {
                $customerDefaultShippingAddress = $customerRepository->getById($customer->getId())->getDefaultShipping();
                if ($customerDefaultShippingAddress) {
                    try {
                        $customerShippingAddress = $this->customerAddressRepository->getById($customerDefaultShippingAddress);
                    } catch (\Magento\Framework\Exception\LocalizedException $exception) { }
                }
            }
            if (isset($customerShippingAddress)) {
                if (!$hasDefaultShipping) {
                    $customerShippingAddress->setIsDefaultShipping(true);
                    $hasDefaultShipping = true;
                    if (!$hasDefaultBilling && !$quoteBillingAddress->getSaveInAddressBook()) {
                        $customerShippingAddress->setIsDefaultBilling(true);
                        $hasDefaultBilling = true;
                    }
                }
                $customerShippingAddress->setCustomerId($quote->getCustomerId());
                $this->customerAddressRepository->save($customerShippingAddress);
                $quote->addCustomerAddress($customerShippingAddress);
                $quoteShippingAddress->setCustomerAddressData($customerShippingAddress);
                $customerShippingAddressId = $customerShippingAddress->getId();
                $this->addAddressToSync((int) $customerShippingAddressId);
                $quoteShippingAddress->setCustomerAddressId($customerShippingAddressId);
            }
        }
        if (!$quoteBillingAddress->getCustomerId() || $quoteBillingAddress->getSaveInAddressBook()) {
            if ($quoteBillingAddress->getQuoteId()) {
                $customerBillingAddress = $quoteBillingAddress->exportCustomerAddress();
            } else {
                $customerDefaultBillingAddress = $customerRepository->getById($customer->getId())->getDefaultBilling();
                if ($customerDefaultBillingAddress) {
                    try {
                        $customerBillingAddress = $this->customerAddressRepository->getById($customerDefaultBillingAddress);
                    } catch (\Magento\Framework\Exception\LocalizedException $exception) { }
                }
            }
            if (isset($customerBillingAddress)) {
                if (!$hasDefaultBilling) {
                    if (!$hasDefaultShipping) {
                        $customerBillingAddress->setIsDefaultShipping(true);
                    }
                    $customerBillingAddress->setIsDefaultBilling(true);
                }
                $customerBillingAddress->setCustomerId($quote->getCustomerId());
                $this->customerAddressRepository->save($customerBillingAddress);
                $quote->addCustomerAddress($customerBillingAddress);
                $quoteBillingAddress->setCustomerAddressData($customerBillingAddress);
                $customerBillingAddressId = $customerBillingAddress->getId();
                $this->addAddressToSync((int) $customerBillingAddressId);
                $quoteBillingAddress->setCustomerAddressId($customerBillingAddressId);
            }
        }
        if ($quoteShippingAddress && !$quoteShippingAddress->getCustomerId() && !$hasDefaultBilling) {
            $quoteShippingAddress->setIsDefaultBilling(true);
        }
        $customerManagement->validateAddresses($quote);
        $customerManagement->populateCustomerInfo($quote);
    }
    
    /**
     * Copy quote address to order
     * 
     * @param \Magento\Quote\Api\Data\AddressInterface $quoteAddress
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param array $orderData
     * @return void
     */
    private function copyQuoteAddressToOrder(\Magento\Quote\Api\Data\AddressInterface $quoteAddress, \Magento\Sales\Api\Data\OrderInterface $order, array $orderData = []): void
    {
        $this->getSubjectPropertyValue('dataObjectHelper')->mergeDataObjects(
            \Magento\Sales\Api\Data\OrderInterface::class,
            $order,
            $this->getSubjectPropertyValue('quoteAddressToOrder')->convert($quoteAddress, $orderData)
        );
    }
    
    /**
     * Convert quote address to order address
     * 
     * @param \Magento\Quote\Api\Data\AddressInterface $quoteAddress
     * @return \Magento\Sales\Api\Data\OrderAddressInterface
     */
    private function convertQuoteAddressToOrderAddress(\Magento\Quote\Api\Data\AddressInterface $quoteAddress): \Magento\Sales\Api\Data\OrderAddressInterface
    {
        return $this->getSubjectPropertyValue('quoteAddressToOrderAddress')->convert(
            $quoteAddress,
            [
                'address_type' => $quoteAddress->getAddressType(),
                'email' => $quoteAddress->getQuote()->getCustomerEmail(),
            ]
        );
    }
    
    /**
     * Create order items
     * 
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return array
     */
    private function createOrderItems(\Magento\Quote\Api\Data\CartInterface $quote): array
    {
        $orderItems = [];
        $quoteItemToOrderItem = $this->getSubjectPropertyValue('quoteItemToOrderItem');
        foreach ($quote->getAllItems() as $quoteItem) {
            $quoteItemId = $quoteItem->getId();
            if (!empty($orderItems[$quoteItemId])) {
                continue;
            }
            $parentQuoteItemId = $quoteItem->getParentItemId();
            if ($parentQuoteItemId && !isset($orderItems[$parentQuoteItemId])) {
                $orderItems[$parentQuoteItemId] = $quoteItemToOrderItem->convert($quoteItem->getParentItem(), ['parent_item' => null]);
            }
            $parentOrderItem = isset($orderItems[$parentQuoteItemId]) ? $orderItems[$parentQuoteItemId] : null;
            $orderItems[$quoteItemId] = $quoteItemToOrderItem->convert($quoteItem, ['parent_item' => $parentOrderItem]);
        }
        return array_values($orderItems);
    }

    /**
     * Get payment method checks
     * 
     * @return array
     */
    private function getPaymentMethodChecks(): array
    {
        return [
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_CHECKOUT,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_COUNTRY,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_CURRENCY,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_ZERO_TOTAL,
        ];
    }

    /**
     * Prepare quote payment
     * 
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @return void
     */
    private function prepareQuotePayment(
        \Magento\Quote\Api\Data\CartInterface $quote,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod = null
    ): void
    {
        if ($paymentMethod === null) {
            $quote->collectTotals();
            return;
        }
        $paymentMethod->setChecks($this->getPaymentMethodChecks());
        $quotePayment = $quote->getPayment();
        $quotePayment->setQuote($quote);
        $quotePayment->importData($paymentMethod->getData());
    }

    /**
     * Prepare quote customer
     * 
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return void
     */
    private function prepareQuoteCustomer(\Magento\Quote\Api\Data\CartInterface $quote): void
    {
        if ($quote->getCheckoutMethod() !== \Magento\Quote\Api\CartManagementInterface::METHOD_GUEST) {
            return;
        }
        $quote->setCustomerId(null);
        $billingAddress = $quote->getBillingAddress();
        $quote->setCustomerEmail($billingAddress->getEmail());
        if ($quote->getCustomerFirstname() === null && $quote->getCustomerLastname() === null) {
            $quote->setCustomerFirstname($billingAddress->getFirstname());
            $quote->setCustomerLastname($billingAddress->getLastname());
            if ($billingAddress->getMiddlename() === null) {
                $quote->setCustomerMiddlename($billingAddress->getMiddlename());
            }
        }
        $quote->setCustomerIsGuest(true);
        $quote->setCustomerGroupId(\Magento\Customer\Api\Data\GroupInterface::NOT_LOGGED_IN_ID);
    }
    
    /**
     * Prepare quote
     * 
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return void
     */
    private function prepareQuote(\Magento\Quote\Api\Data\CartInterface $quote): void
    {
        $remoteAddress = $this->remoteAddress->getRemoteAddress();
        if ($remoteAddress === false) {
            return;
        }
        $quote->setRemoteIp($remoteAddress);
        $quote->setXForwardedFor($this->request->getServer('HTTP_X_FORWARDED_FOR'));
    }
    
    /**
     * Update checkout session
     * 
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param array $orders
     * @return void
     */
    private function updateCheckoutSession(\Magento\Quote\Api\Data\CartInterface $quote, array $orders): void
    {
        $quoteId = $quote->getId();
        $checkoutSession = $this->getSubjectPropertyValue('checkoutSession');
        $checkoutSession->setLastQuoteId($quoteId);
        $checkoutSession->setLastSuccessQuoteId($quoteId);
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
     * Add address to sync
     * 
     * @param int $addressId
     * @return void
     */
    private function addAddressToSync(int $addressId): void
    {
        $addressesToSync = $this->getSubjectPropertyValue('addressesToSync');
        $addressesToSync[] = $addressId;
        $this->setSubjectPropertyValue('addressesToSync', $addressesToSync);
    }
}