<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product;

/**
 * Product source quote management
 */
class SourceQuoteManagement implements \Ecombricks\InventoryCustomerSourceSelection\Api\Catalog\Product\SourceQuoteManagementInterface
{
    /**
     * Product source quote object factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Api\Catalog\Data\Product\SourceQuoteInterfaceFactory
     */
    private $productSourceQuoteObjectFactory;
    
    /**
     * Configuration
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product\SourceQuote\Config 
     */
    private $config;
    
    /**
     * Shipping rates converter
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product\SourceQuote\ShippingRatesConverter
     */
    private $shippingRatesConverter;
    
    /**
     * Totals converter
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product\SourceQuote\TotalsConverter
     */
    private $totalsConverter;
    
    /**
     * Get current salable source items
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetCurrentSalableSourceItems
     */
    private $getCurrentSalableSourceItems;
    
    /**
     * Quote address wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory
     */
    private $quoteAddressWrapperFactory;
    
    /**
     * User context
     * 
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    private $userContext;
    
    /**
     * Product repository
     * 
     * @var \Magento\Catalog\Model\ProductRepository
     */
    private $productRepository;

    /**
     * Customer repository
     * 
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * Data object processor
     * 
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    private $dataObjectProcessor;
    
    /**
     * Quote factory
     * 
     * @var \Magento\Quote\Model\QuoteFactory
     */
    private $quoteFactory;

    /**
     * Store manager
     * 
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Api\Catalog\Data\Product\SourceQuoteInterfaceFactory $productSourceQuoteObjectFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product\SourceQuote\Config $config
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product\SourceQuote\ShippingRatesConverter $shippingRatesConverter
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product\SourceQuote\TotalsConverter $totalsConverter
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetCurrentSalableSourceItems $getCurrentSalableSourceItems
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory
     * @param \Magento\Authorization\Model\UserContextInterface $userContext
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Api\Catalog\Data\Product\SourceQuoteInterfaceFactory $productSourceQuoteObjectFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product\SourceQuote\Config $config,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product\SourceQuote\ShippingRatesConverter $shippingRatesConverter,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Catalog\Product\SourceQuote\TotalsConverter $totalsConverter,
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetCurrentSalableSourceItems $getCurrentSalableSourceItems,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\AddressFactory $quoteAddressWrapperFactory,
        \Magento\Authorization\Model\UserContextInterface $userContext,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->productSourceQuoteObjectFactory = $productSourceQuoteObjectFactory;
        $this->config = $config;
        $this->shippingRatesConverter = $shippingRatesConverter;
        $this->totalsConverter = $totalsConverter;
        $this->getCurrentSalableSourceItems = $getCurrentSalableSourceItems;
        $this->quoteAddressWrapperFactory = $quoteAddressWrapperFactory;
        $this->userContext = $userContext;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->quoteFactory = $quoteFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Get list
     * 
     * @param string $sku
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @param mixed $request
     * @return \Ecombricks\InventoryCustomerSourceSelection\Api\Catalog\Data\Product\SourceQuoteInterface[]
     */
    public function getList($sku, $address, $request = [])
    {
        if (!$this->config->isEnabled()) {
            return [];
        }
        $productSourceQuotes = [];
        $preparedRequest = $this->prepareRequest($request);
        foreach ($this->getSourceQuotes((string) $sku, $address, $preparedRequest) as $quote) {
            $productSourceQuotes[] = $this->productSourceQuoteObjectFactory->create()
                ->setSourceCode($quote->getCurrentSourceCode())
                ->setShippingMethod($this->getSourceQuoteShippingMethod($quote))
                ->setShippingRates($this->shippingRatesConverter->process($quote))
                ->setTotals($this->totalsConverter->process($quote));
        }
        if (empty($productSourceQuotes)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('No product quotes found.'));
        }
        return $productSourceQuotes;
    }

    /**
     * Get store
     * 
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    private function getStore(): \Magento\Store\Api\Data\StoreInterface
    {
        return $this->storeManager->getStore();
    }

    /**
     * Get product
     * 
     * @param string $sku
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    private function getProduct(string $sku): \Magento\Catalog\Api\Data\ProductInterface
    {
        return $this->productRepository->get($sku);
    }

    /**
     * Get customer
     * 
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    private function getCustomer(): ?\Magento\Customer\Api\Data\CustomerInterface
    {
        $customerId = ($this->userContext->getUserType() === \Magento\Authorization\Model\UserContextInterface::USER_TYPE_CUSTOMER) ? 
            $this->userContext->getUserId() : 
            null;
        if (!$customerId) {
            return null;
        }
        return $this->customerRepository->getById($customerId);
    }

    /**
     * Prepare request
     * 
     * @param mixed $request
     * @return array
     */
    private function prepareRequest($request): array
    {
        if (!is_array($request)) {
            return [];
        }
        return $request;
    }
    
    /**
     * Get request object
     * 
     * @param array $request
     * @param string $sourceCode
     * @return \Magento\Framework\DataObject
     */
    private function getRequestObject(array $request, string $sourceCode): \Magento\Framework\DataObject
    {
        return new \Magento\Framework\DataObject(array_merge($request, ['source' => $sourceCode]));
    }
    
    /**
     * Get request shipping method
     * 
     * @param array $request
     * @param string $sourceCode
     * @return string|null
     */
    private function getRequestShippingMethod(array $request, string $sourceCode): ?string
    {
        $shippingMethod = null;
        if (!empty($request['shipping_methods'])) {
            $shippingMethods = $request['shipping_methods'];
            if (!empty($shippingMethods[$sourceCode])) {
                $shippingMethod = $shippingMethods[$sourceCode];
            }
        }
        return $shippingMethod;
    }

    /**
     * Get source codes
     * 
     * @param string $sku
     * @param string $sourceCode
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @param array $request
     * @return \Magento\Quote\Api\Data\CartInterface
     */
    private function getSourceQuote(string $sku, string $sourceCode, \Magento\Quote\Api\Data\AddressInterface $address, array $request): \Magento\Quote\Api\Data\CartInterface
    {
        $quote = $this->quoteFactory->create();
        $quote->setCurrentSourceCode($sourceCode);
        $quote->setStore($this->getStore());
        $customer = $this->getCustomer();
        if ($customer) {
            $quote->setCustomer($customer);
            $quote->setCustomerIsGuest(0);
        } else {
            $quote->setCustomerIsGuest(1);
        }
        $addressData = $this->dataObjectProcessor->buildOutputDataArray($address, \Magento\Quote\Api\Data\AddressInterface::class);
        $product = $this->getProduct($sku);
        $quoteItem = $quote->addProduct($product, $this->getRequestObject($request, $sourceCode));
        $quote->getBillingAddress()->addData($addressData);
        if (!$product->isVirtual()) {
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->addData($addressData);
            $shippingMethod = $this->getRequestShippingMethod($request, $sourceCode);
            if ($shippingMethod) {
                $this->quoteAddressWrapperFactory->create($shippingAddress)->setShippingMethod($sourceCode, $shippingMethod);
            }
            $shippingAddress->setCollectShippingRates(true);
        }
        if (is_string($quoteItem)) {
            throw new \Magento\Framework\Exception\LocalizedException(__($quoteItem));
        }
        $quote->collectTotals();
        return $quote;
    }
    
    /**
     * Get source codes
     * 
     * @param string $sku
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @param array $request
     * @return \Magento\Quote\Api\Data\CartInterface[]
     */
    private function getSourceQuotes(string $sku, \Magento\Quote\Api\Data\AddressInterface $address, array $request): array
    {
        $quotes = [];
        $sourceCodes = array_keys($this->getCurrentSalableSourceItems->execute($sku));
        if ($this->config->isCurrentSourceOnly()) {
            $currentSourceCode = $request['source'] ?? null;
            $sourceCodes = $currentSourceCode && in_array($currentSourceCode, $sourceCodes) ? [$currentSourceCode] : [];
        }
        foreach ($sourceCodes as $sourceCode) {
            $quotes[(string) $sourceCode] = $this->getSourceQuote($sku, (string) $sourceCode, $address, $request);
        }
        return $quotes;
    }

    /**
     * Get source quote shipping method
     * 
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return string|null
     */
    private function getSourceQuoteShippingMethod($quote): ?string
    {
        if ($quote->isVirtual()) {
            return null;
        }
        return $this->quoteAddressWrapperFactory->create($quote->getShippingAddress())->getShippingMethod($quote->getCurrentSourceCode());
    }
}