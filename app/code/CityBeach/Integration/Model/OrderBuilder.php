<?php
/**
 * Created by PhpStorm.
 * User: will
 * Date: 20/10/17
 * Time: 10:08 AM
 */

namespace CityBeach\Integration\Model;

use CityBeach\Integration\Api\Data\OrderInterface;

class OrderBuilder
{
    const PAYMENT = 'citybeachpayment';
    const PAYMENT_TITLE = 'Payment';
    const SHIPPING = 'citybeachshipping';
    const SHIPPING_TITLE = 'Shipping';
    const SHIPPING_DATA_KEY = 'citybeachshipping_data';

    private $customOrder;

    /** @var  \Magento\Store\Api\Data\StoreInterface $store */
    protected $store;
    /** @var  \Magento\Quote\Model\Quote $quote */
    protected $quote;
    /** @var \Magento\Sales\Model\Order $order */
    protected $order;
    /** @var \Magento\Sales\Model\Order\Invoice $invoice */
    protected $invoice;

    protected $message;

    protected $shipping_carrier;
    protected $shipping_method;
    protected $shipping_method_code;

    /* @var \Magento\Framework\ObjectManagerInterface $objectManager */
    private $objectManager;
    /* @var \Magento\Framework\Registry $registryModel */
    private $registryModel;
    /** @var  \Magento\Quote\Model\QuoteFactory $quoteFactory */
    protected $quoteFactory;
    /** @var  \Magento\Checkout\Model\Session $checkoutSession */
    protected $checkoutSession;
    /** @var  \Magento\Directory\Model\CurrencyFactory $currencyFactory */
    protected $currencyFactory;
    /** @var  \Magento\Tax\Model\Calculation $calculation */
    protected $calculation;
    /** @var  \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
    protected $productRepository;
    /** @var  \Magento\Store\Model\StoreManagerInterface $storeManager */
    protected $storeManager;
    /** @var  \Magento\Shipping\Model\Config $shippingConfig */
    private $shippingConfig;
    /** @var  \Magento\Sales\Model\ResourceModel\Order\Status\Collection $orderStatusCollection */
    private $orderStatusCollection;
    /** @var  \Magento\Quote\Model\QuoteManagement $quoteManagement */
    private $quoteManagement;
    /* @var \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder */
    private $transactionBuilder;
    /* @var \Magento\Sales\Model\Service\InvoiceService $invoiceService */
    private $invoiceService;
    /* @var \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface */
    private $cartRepositoryInterface;
    /* @var \Magento\Quote\Api\CartManagementInterface $cartManagementInterface */
    private $cartManagementInterface;
    /** @var \Magento\Sales\Model\Order $orderFactory */
    private $orderFactory;

    private $products;
    private $customOrderItems;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registryModel,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Tax\Model\Calculation $calculation,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Sales\Model\ResourceModel\Order\Status\Collection $orderStatusCollection,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
        \Magento\Sales\Model\Order $orderFactory,
        OrderInterface $customOrder)
    {
        $this->objectManager = $objectManager;
        $this->registryModel = $registryModel;
        $this->quoteFactory = $quoteFactory;
        $this->checkoutSession = $checkoutSession;

        $this->currencyFactory = $currencyFactory;
        $this->calculation = $calculation;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->shippingConfig = $shippingConfig;
        $this->orderStatusCollection = $orderStatusCollection;
        $this->quoteManagement = $quoteManagement;
        $this->transactionBuilder = $transactionBuilder;
        $this->invoiceService = $invoiceService;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->orderFactory = $orderFactory;

        $this->customOrder = $customOrder;

        $this->products = [];
        $this->customOrderItems = [];
    }

    /**
     * Getter method for the created Magento Quote.
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * Getter method for the created Magento Order.
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Getter method for the order id.
     *
     * @return mixed
     */
    public function getOrderId() {
        return $this->order->getId();
    }

    /**
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }


    /**
     * Create an order.
     *
     * @throws \Exception on a validation error or order processing failure.
     */
    public function execute() {
        try {
            $this->message = '';
            // NOTE: The order of the following steps is importance.
            // Basic order structure validation.
            $this->customOrder->validate();
            // Find the store for the order.
            $this->findStore();
            // Validate the environment.
            $this->validateSetup();
            // Find the products for the order.
            $this->findProducts();

            $this->openQuote();
            $this->tax();
            $this->currency();
            $this->shippingData();
            $this->items();
            $this->quotePayment();
            $this->shipping();
            $this->quote->collectTotals();

            $this->convertToOrder();
            $this->payment();
            $this->order->save();

            $this->checkPayments();
            $this->status();

            $this->order->save();

            $this->convertToInvoice();

        } catch (\Exception $exception) {
            if ($this->quote && ! $this->quote->isObjectNew()) {
                $this->quote->setIsActive(false)->save();
            }
            if ($this->order && ! $this->order->isObjectNew()) {
                $this->order->cancel()->save();
            }
            throw $exception;
        }
    }

    private function findStore() {
        $storeId = $this->customOrder->getStoreId();
        $store = $this->storeManager->getStore($storeId);

        if (is_null($store->getId())) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Selected Magento Store does not exist.')
            );
        }

        $this->store = $store;
    }

    private function findProducts() {
        $products = [];
        $items = [];

        // Check the products exists and index the products and items by external id.
        foreach ($this->customOrder->getProducts() as $item) {
            $product = $this->productRepository->getById($item->getExternalId());
            if (!$product->getEntityId()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('invalid id: ' . $item->getExternalId() . ' for item with sku: ' . $item->getSku())
                );
            } elseif ($product->getSku() !== $item->getSku() ) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('mis-match id: ' . $item->getExternalId() . ' for sku: ' . $item->getSku())
                );
            } else {
                $products[$item->getExternalId()] = $product;
                $items[$item->getExternalId()] = $item;
            }
        }

        $this->products = $products;
        $this->customOrderItems = $items;
    }

    private function validateSetup() {
        // Check the shipping method structure.
        $shipping_carrier_code = $this->customOrder->getShipping()->getCarrier();
        $shipping_method_code = $this->customOrder->getShipping()->getMethod();
        $this->shipping_method_code = $shipping_carrier_code . '_' . $shipping_method_code;

        // Look up all carriers.
        $shipping_carriers = $this->shippingConfig->getActiveCarriers();
        if (!isset($shipping_carriers[$shipping_carrier_code])) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Cannot find an carrier for shipping method')
            );
        }
        $this->shipping_carrier = $shipping_carriers[$shipping_carrier_code];

        // Check the carrier for the method.
        $shipping_methods = $this->shipping_carrier->getAllowedMethods();
        if (!isset($shipping_methods[$shipping_method_code])) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Cannot find an method for shipping method')
            );
        }
        $this->shipping_method = $shipping_methods[$shipping_method_code];

        // Check the custom order status is available.
        if (!empty($this->customOrder->getCustomOrderStatus())) {
            $custom_order_status = $this->customOrder->getCustomOrderStatus();
            $statuses = $this->orderStatusCollection->getItems();
            if (!isset($statuses[$custom_order_status])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Custom order status is not available.')
                );
            }
        }
    }

    private function openQuote() {
        // Initialise the quote.
        $cartId = $this->cartManagementInterface->createEmptyCart();
        $this->quote = $this->cartRepositoryInterface->get($cartId); // load empty cart quote

        $this->quote->setCheckoutMethod('guest'); // 'login', 'guest' or 'register'
        $this->quote->setStore($this->store);
        $this->quote->getStore()->setData('current_currency', $this->quote->getStore()->getBaseCurrency());

        // adding a prefix without the sequencer.
        if ( !empty( $this->customOrder->getOrderPrefix() ) ) {
            $this->quote->reserveOrderId();
            $reserveOrderId = $this->quote->getReservedOrderId();
            if ( $reserveOrderId == null ) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Failed to reserve order id.')
                );
            }
            $reserveOrderId = $this->customOrder->getOrderPrefix() . '-' . $reserveOrderId;
            $this->quote->setReservedOrderId( $reserveOrderId );
        }

        $this->quote->save();

        $this->checkoutSession->replaceQuote($this->quote);

        // Add the Customer.
        $this->quote
            ->setCustomerId(null)
            ->setCustomerEmail($this->customOrder->getCustomer()->getEmail())
            ->setCustomerFirstname($this->customOrder->getCustomer()->getFirstName())
            ->setCustomerLastname($this->customOrder->getCustomer()->getLastName())
            ->setCustomerIsGuest(true)
            ->setCustomerGroupId(\Magento\Customer\Model\Group::NOT_LOGGED_IN_ID);

        // Add the billing address.
        $billingAddress = $this->quote->getBillingAddress();
        $billingAddress->addData($this->customOrder->getBillingAddress()->toArray());
        $billingAddress->setStreet($billingAddress->getStreet());
        $billingAddress->setLimitCarrier($this->customOrder->getShipping()->getCarrier());
        $billingAddress->setShippingMethod($this->shipping_method_code); // Magento 2 - CODE NOT METHOD
        $billingAddress->setCollectShippingRates(false);
        $billingAddress->setShouldIgnoreValidation(true);

        // Add the shipping address.
        $shippingAddress = $this->quote->getShippingAddress();
        $shippingAddress->setSameAsBilling(0);
        $shippingAddress->addData($this->customOrder->getShippingAddress()->toArray());
        $shippingAddress->setStreet($shippingAddress->getStreet());
        $shippingAddress->setLimitCarrier($this->customOrder->getShipping()->getCarrier());
        $shippingAddress->setShippingMethod($this->shipping_method_code);  // Magento 2 - CODE NOT METHOD
        $shippingAddress->setCollectShippingRates(true);
    }


    private function tax() {
        // TODO is this used
        $this->calculation->setCustomer($this->quote->getCustomer());
    }


    private function currency() {
        // Currency setup.
        $customOrderCurrency = $this->customOrder->getCurrency();
        if ($this->isConvertibleCurrency($customOrderCurrency)) {
            $currentCurrency = $this->currencyFactory->create()->load($customOrderCurrency);
        } else {
            $currentCurrency = $this->quote->getStore()->getBaseCurrency();
        }
        $this->quote->getStore()->setData('current_currency', $currentCurrency);
    }


    private function shippingData() {
        // Store the shipping details for use byt the shipping method.
        $this->registryModel->unregister(static::SHIPPING_DATA_KEY);
        $this->registryModel->register(static::SHIPPING_DATA_KEY, $this->customOrder->getShipping());
    }

    private function shipping() {
        // Store the shipping details for use byt the shipping method.
        $this->registryModel->unregister(static::SHIPPING_DATA_KEY);
        $this->registryModel->register(static::SHIPPING_DATA_KEY, $this->customOrder->getShipping());

        $shipping_carrier_code = $this->customOrder->getShipping()->getCarrier();
        $shipping_method_code = $this->customOrder->getShipping()->getMethod();

        /** @var \Magento\Quote\Model\Quote\Address\Rate $shippingRate */
        $shippingRate = $this->objectManager->create('\Magento\Quote\Model\Quote\Address\Rate', []);
        $shippingRate->setCode($this->shipping_method);
        $shippingRate->setMethod($shipping_method_code);
        $shippingRate->setCarrier($shipping_carrier_code);
        $shippingRate->setCarrierTitle($this->shipping_carrier->getConfigData('title'));
        $shippingRate->setMethodTitle($shipping_method_code);

        $this->quote->getShippingAddress()->addShippingRate($shippingRate);
        $this->quote->getShippingAddress()->setShippingMethod($this->shipping_method_code);
    }

    private function items() {
        // Quote Items
        foreach ($this->customOrderItems as $externalId => $customOrderItem) {
            // Clear caches.
            foreach ($this->quote->getAllAddresses() as $address) {
                $address->unsetData('cached_items_all');
                $address->unsetData('cached_items_nominal');
                $address->unsetData('cached_items_nonnominal');
            }

            // Find the product and set the tax class from the product.
            $product = $this->products[$externalId];
            $taxClassId = $product->getTaxClassId();
            $product->setTaxClassId($taxClassId);

            // Create a request.
            $request = new \Magento\Framework\DataObject();
            $request->setQty($customOrderItem->getQuantity());

            $productOriginalPrice = (float)$product->getPrice();
            $price = $customOrderItem->getUnitPrice();
            $product->setPrice($price);
            $product->setSpecialPrice($price);

            // see Mage_Sales_Model_Observer::substractQtyFromQuotes
            $this->quote->setItemsCount($this->quote->getItemsCount() + 1);
            $this->quote->setItemsQty((float)$this->quote->getItemsQty() + $request->getQty());

            $result = $this->quote->addProduct($product, $request);
            if (is_string($result)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($result)
                );
            }

            $quoteItem = $this->quote->getItemByProduct($product);

            if ($quoteItem !== false) {
                $quoteItem->setStoreId($this->quote->getStoreId());
                $quoteItem->setOriginalCustomPrice($customOrderItem->getUnitPrice());
                $quoteItem->setOriginalPrice($productOriginalPrice);
                $quoteItem->setBaseOriginalPrice($productOriginalPrice);
                $quoteItem->setNoDiscount(1);
            }
        }

        $allItems = $this->quote->getAllItems();
        $this->quote->getItemsCollection()->removeAllItems();

        foreach ($allItems as $item) {
            $item->save();
            $this->quote->getItemsCollection()->addItem($item);
        }
    }

    private function convertToOrder() {
        $this->quote = $this->cartRepositoryInterface->get($this->quote->getId());
        $orderId = $this->cartManagementInterface->placeOrder($this->quote->getId());
        $this->order = $this->orderFactory->load($orderId);
        // Empty the quote.
        $this->quote->setIsActive(false)->save();
    }

    private function quotePayment() {
        $payment = $this->customOrder->getPayment();
        $transactionId = $payment->getTransactionId();
        $transactionDate = $payment->getTransactionDate();

        $transactions = [];
        $transactions[] = array(
            'transaction_id'   => $transactionId,
            'transaction_date' => $transactionDate,
        );

        $paymentData = array(
            'method'                => OrderBuilder::PAYMENT,
            'payment_method'        => OrderBuilder::PAYMENT_TITLE,
            'transactions'          => $transactions,
        );

        $quotePayment = $this->quote->getPayment();
        $quotePayment->importData($paymentData);
    }


    private function payment() {
        // Payment details.
        $payment = $this->customOrder->getPayment();
        $transactionId = $payment->getTransactionId();
        $transactionDate = $payment->getTransactionDate();

        $additional_payment_information = [
            'payment_type' => $payment->getPaymentType(),
            'transaction_id' => $transactionId,
            'transaction_date' => $transactionDate,
            'user_reference' => $payment->getUserReference(),
        ];
        if ( !empty( $payment->getTransactionSource() ) ) {
            $additional_payment_information['transaction_source'] = $payment->getTransactionSource();
        }

        if ( !empty( $this->customOrder->getMarketplaceCode() ) ) {
            $additional_payment_information['marketplace'] = $this->customOrder->getMarketplaceCode();
        }

        /** @var  \Magento\Sales\Model\Order\Payment $quotePayment */
        $orderPayment = $this->order->getPayment();
        $orderPayment->setAdditionalInformation($additional_payment_information);

        $transaction = $this->transactionBuilder
            ->setOrder($this->order)
            ->setPayment($orderPayment)
            ->setTransactionId($transactionId)
            ->setFailSafe(true)
            ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);
        $orderPayment->addTransactionCommentsToOrder($transaction, '');

        $orderPayment->setParentTransactionId(null);
        $orderPayment->save();

    }

    private function checkPayments() {
        $payment = $this->customOrder->getPayment();
        $amount = $payment->getAmount();

        // Report on unexpected total.
        if ($this->order->getGrandTotal() !== $amount) {
            if (!empty($this->message)) {
                $this->message = $this->message . ', ';
            }
            $this->message = $this->message . 'Order totals do not match, ' . $this->order->getGrandTotal() . ' vs ' . $amount;

            if ($this->order->getShippingInclTax() != ($this->customOrder->getShipping()->getPrice() + $this->customOrder->getShipping()->getTax())) {
                $this->message = $this->message . ' - shipping amount differs';
            }
        }
    }

    private function status() {
        // Add the message to the history.
        if (!empty($this->message)) {
            $this->order->addStatusHistoryComment($this->message);
        }
        // Add in delivery Instructions
        if (!empty($this->customOrder->getDeliveryInstructions())) {
            $this->order->addStatusHistoryComment('Delivery Instructions: ' . $this->customOrder->getDeliveryInstructions());
        }
        // Add in marketplace
        if (!empty($this->customOrder->getMarketplaceCode())) {
            $this->order->addStatusHistoryComment('Marketplace: ' . $this->customOrder->getMarketplaceCode());
        }
        // Set the status and state.
        if (!empty($this->customOrder->getCustomOrderStatus())) {
            $this->order->addStatusHistoryComment('Order creation', $this->customOrder->getCustomOrderStatus());
            $this->order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
        }
    }


    private function convertToInvoice() {
      if (!$this->order->canInvoice()) {
        if ($this->order->canUnhold() || $this->order->isPaymentReview()) {
          throw new \Magento\Framework\Exception\LocalizedException(
            __('Unable to create invoice from order (unhold or payment review).')
          );
        }
        $state = $this->order->getState();
        if ($this->order->isCanceled() || $state === \Magento\Sales\Model\Order::STATE_COMPLETE || $state === \Magento\Sales\Model\Order::STATE_CLOSED) {
          throw new \Magento\Framework\Exception\LocalizedException(
            __('Unable to create invoice from order (state issue): ' . $state)
          );
        }

        if ($this->order->getActionFlag(\Magento\Sales\Model\Order::ACTION_FLAG_INVOICE) === false) {
          throw new \Magento\Framework\Exception\LocalizedException(
            __('Unable to create invoice from order (action flag invoice): ' . $state)
          );
        }

        throw new \Magento\Framework\Exception\LocalizedException(
          __('Unable to create invoice from order (other): ' . $state)
        );
      }

      $this->invoice = $this->invoiceService->prepareInvoice($this->order);
      $this->invoice->register();
      $this->invoice->save();
      $this->order->save();
    }


    //
    // Currency helpers - TODO handle currency conversion - the following are reference methods for the conversion.
    //

    private function isConvertibleCurrency($currencyCode) {
        if ($this->isBaseCurrency($currencyCode)
            || !$this->isAllowedCurrency($currencyCode)
            || $this->getConvertRateFromBaseCurrency($currencyCode) == 0
        ) {
            return false;
        }

        return true;
    }

    private function isBaseCurrency($currencyCode)
    {
        $baseCurrency = $this->store->getBaseCurrencyCode();
        return $baseCurrency == $currencyCode;
    }

    private function isAllowedCurrency($currencyCode)
    {
        $allowedCurrencies = $this->store->getAvailableCurrencyCodes();
        return in_array($currencyCode, $allowedCurrencies);
    }

    private function getConvertRateFromBaseCurrency($currencyCode, $precision = 2)
    {
        if (!$this->isAllowedCurrency($currencyCode)) {
            return 0;
        }

        $precision = (int)$precision;

        if ($precision <= 0) {
            $precision = 2;
        }

        $rate = (float)$this->storeManager->store->getBaseCurrency()->getRate($currencyCode);

        return round($rate, $precision);
    }

}