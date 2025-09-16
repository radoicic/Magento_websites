<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote;

/**
 * Quote address wrapper
 */
class Address extends \Ecombricks\Common\DataObject\Wrapper
{
    /**
     * Wrapper factory
     * 
     * @var \Ecombricks\Common\DataObject\WrapperFactory 
     */
    private $wrapperFactory;

    /**
     * Current source provider
     * 
     * @var \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface 
     */
    private $currentSourceProvider;

    /**
     * Get source by source code
     * 
     * @var \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode
     */
    private $getSourceBySourceCode;

    /**
     * Quote item wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory
     */
    private $quoteItemWrapperFactory;

    /**
     * Quote wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\QuoteFactory
     */
    private $quoteWrapperFactory;

    /**
     * Store manager
     * 
     * @var \Magento\Store\Model\StoreManagerInterface 
     */
    private $storeManager;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface $currentSourceProvider
     * @param \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode $getSourceBySourceCode
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory $quoteItemWrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\QuoteFactory $quoteWrapperFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory,
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface $currentSourceProvider,
        \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode $getSourceBySourceCode,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory $quoteItemWrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\QuoteFactory $quoteWrapperFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        parent::__construct($objectReflectionFactory);
        $this->wrapperFactory = $wrapperFactory;
        $this->currentSourceProvider = $currentSourceProvider;
        $this->getSourceBySourceCode = $getSourceBySourceCode;
        $this->quoteItemWrapperFactory = $quoteItemWrapperFactory;
        $this->quoteWrapperFactory = $quoteWrapperFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * After load
     * 
     * @return void
     */
    public function afterLoad(): void
    {
        $this->setShippingMethods($this->getResourceShippingMethodWrapper()->getSourceOptions($this->getObject()->getId()));
    }

    /**
     * After save
     * 
     * @return void
     */
    public function afterSave(): void
    {
        $this->getResourceShippingMethodWrapper()->saveSourceOptions($this->getObject()->getId(), $this->getShippingMethods());
    }

    /**
     * Get source codes
     * 
     * @return array
     */
    public function getSourceCodes(): array
    {
        $sourceCodes = [];
        $quoteAddress = $this->getObject();
        if (!$quoteAddress->getQuote()) {
            return $sourceCodes;
        }
        $hasCache = $quoteAddress->hasData('cached_items_all');
        foreach ($quoteAddress->getAllItems() as $item) {
            $sourceCode = (string) $this->quoteItemWrapperFactory->create($item)->getSourceCode();
            if (empty($sourceCode)) {
                continue;
            }
            if (in_array($sourceCode, $sourceCodes)) {
                continue;
            }
            $sourceCodes[] = $sourceCode;
        }
        if (!$hasCache) {
            $quoteAddress->unsetData(['cached_items_all']);
        }
        return $sourceCodes;
    }

    /**
     * Set limit carriers
     * 
     * @param array $limitCarriers
     * @return void
     */
    public function setLimitCarriers(array $limitCarriers): void
    {
        $this->getLimitCarrierWrapper()->setSourceOptions($limitCarriers);
    }
    
    /**
     * Get limit carriers
     * 
     * @return array
     */
    public function getLimitCarriers(): array
    {
        return $this->getLimitCarrierWrapper()->getSourceOptions();
    }

    /**
     * Set limit carrier
     * 
     * @param string $sourceCode
     * @param string $limitCarrier
     * @return void
     */
    public function setLimitCarrier(string $sourceCode, string $limitCarrier): void
    {
        $this->getLimitCarrierWrapper()->setSourceOption($sourceCode, $limitCarrier);
    }

    /**
     * Get limit carrier
     * 
     * @param string $sourceCode
     * @return string|null
     */
    public function getLimitCarrier(string $sourceCode): ?string
    {
        return $this->getLimitCarrierWrapper()->getSourceOption($sourceCode);
    }

    /**
     * Set shipping methods
     * 
     * @param array $shippingMethods
     * @return void
     */
    public function setShippingMethods(array $shippingMethods): void
    {
        $this->getShippingMethodWrapper()->setSourceOptions($shippingMethods);
    }
    
    /**
     * Get shipping methods
     * 
     * @return array
     */
    public function getShippingMethods(): array
    {
        return $this->getShippingMethodWrapper()->getSourceOptions();
    }

    /**
     * Set shipping method
     * 
     * @param string $sourceCode
     * @param string $shippingMethod
     * @return void
     */
    public function setShippingMethod(string $sourceCode, string $shippingMethod): void
    {
        $this->getShippingMethodWrapper()->setSourceOption($sourceCode, $shippingMethod);
    }
    
    /**
     * Get shipping method
     * 
     * @param string $sourceCode
     * @return string|null
     */
    public function getShippingMethod(string $sourceCode): ?string
    {
        return $this->getShippingMethodWrapper()->getSourceOption($sourceCode);
    }

    /**
     * Set source code
     * 
     * @param string $sourceCode
     * @return void
     */
    public function setSourceCode(string $sourceCode): void
    {
        $quoteAddress = $this->getObject();
        $quoteAddress->getExtensionAttributes()->setSourceCode($sourceCode);
        $this->filterSourceItems();
        if ($quoteAddress->getAddressType() == \Magento\Quote\Model\Quote\Address::ADDRESS_TYPE_SHIPPING) {
            $this->setShippingMethods([$sourceCode => $this->getShippingMethod($sourceCode)]);
            $this->setLimitCarriers([$sourceCode => $this->getLimitCarrier($sourceCode)]);
        }
        $this->unsetTotals();
    }
    
    /**
     * Set source code
     * 
     * @return string|null
     */
    public function getSourceCode(): ?string
    {
        return $this->getObject()->getExtensionAttributes()->getSourceCode();
    }
    
    /**
     * Create source clone
     * 
     * @param string $sourceCode
     * @return \Magento\Quote\Api\Data\AddressInterface
     */
    public function createSourceClone($sourceCode)
    {
        $quoteAddress = $this->getObject();
        $quote = $quoteAddress->getQuote();
        $quoteWrapper = $this->quoteWrapperFactory->create($quote);
        $sourceQuote = $quoteWrapper->createSourceClone($sourceCode);
        $sourceQuoteAddress = clone $quoteAddress;
        $sourceQuoteAddress->setExtensionAttributes(clone $quoteAddress->getExtensionAttributes());
        $sourceQuoteAddress->setId($quoteAddress->getId());
        $sourceQuoteAddress->setQuote($sourceQuote);
        $this->setObject($sourceQuoteAddress);
        $this->setSourceCode($sourceCode);
        $this->setObject($quoteAddress);
        $this->getPropertyValue('totalsCollector')->collectAddressTotals($sourceQuote, $sourceQuoteAddress);
        return $sourceQuoteAddress;
    }
    
    /**
     * Request shipping rates
     * 
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return bool
     */
    public function requestShippingRates(\Magento\Quote\Model\Quote\Item\AbstractItem $item = null): bool
    {
        if ($item) {
            return $this->requestItemShippingRates($item);
        }
        $quoteAddress = $this->getObject();
        if (!$quoteAddress->getQuote()) {
            return false;
        }
        $found = true;
        $sourceCode = $this->getSourceCode();
        if (!$sourceCode) {
            foreach ($this->getSourceCodes() as $sourceCode) {
                $sourceFound = $this->requestAddressShippingRates($this->createSourceClone($sourceCode));
                $found = $found && $sourceFound;
            }
        } else {
            $found = $this->requestAddressShippingRates($this->createSourceClone($sourceCode));
        }
        return $found;
    }

    /**
     * Get grouped all shipping rates
     * 
     * @return array
     */
    public function getGroupedAllShippingRates()
    {
        $quoteAddress = $this->getObject();
        $groupedRates = [];
        foreach ($quoteAddress->getShippingRatesCollection() as $rate) {
            $sourceCode = $rate->getSourceCode();
            $carrierCode = $rate->getCarrier();
            $carrier = $this->getPropertyValue('carrierFactory')->get($carrierCode);
            if ($rate->isDeleted() || !$carrier) {
                continue;
            }
            if (!isset($groupedRates[$sourceCode])) {
                $groupedRates[$sourceCode] = [];
            }
            $sourceRates =& $groupedRates[$sourceCode];
            if (!isset($sourceRates[$carrierCode])) {
                $sourceRates[$carrierCode] = [];
            }
            $sourceRates[$carrierCode][] = $rate;
            $sourceRates[$carrierCode][0]->carrier_sort_order = $carrier->getSortOrder();
            uasort($sourceRates, [$this, 'compareCarriers']);
        }
        return $groupedRates;
    }

    /**
     * Get source shipping rate by code
     * 
     * @param string $sourceCode
     * @param string $code
     * @return \Magento\Quote\Model\Quote\Address\Rate|null
     */
    public function getShippingRateByCode(string $sourceCode, string $code): ?\Magento\Quote\Model\Quote\Address\Rate
    {
        $quoteAddress = $this->getObject();
        foreach ($quoteAddress->getShippingRatesCollection() as $rate) {
            if (($rate->getSourceCode() == $sourceCode) && ($rate->getCode() === $code)) {
                return $rate;
            }
        }
        return null;
    }

    /**
     * Get shipping rates by codes
     * 
     * @param array $codes
     * @return \Magento\Quote\Model\Quote\Address\Rate[]
     */
    public function getShippingRatesByCodes($codes): array
    {
        if (!is_array($codes) || empty($codes)) {
            return [];
        }
        $shippingRates = [];
        $sourceCodes = $this->getSourceCodes();
        foreach ($this->getSourceCodes() as $sourceCode) {
            if (empty($codes[$sourceCode])) {
                $shippingRates = [];
                break;
            }
            $shippingRates[$sourceCode] = $this->getShippingRateByCode($sourceCode, $codes[$sourceCode]);
        }
        $this->getShippingMethodWrapper()->setSourceCodes($sourceCodes);
        return $this->getShippingMethodWrapper()->filterSourceOptions($shippingRates);
    }

    /**
     * Get current shipping rates
     * 
     * @return \Magento\Quote\Model\Quote\Address\Rate[]
     */
    public function getCurrentShippingRates(): array
    {
        $shippingRates = [];
        $sourceCodes = $this->getSourceCodes();
        foreach ($this->getSourceCodes() as $sourceCode) {
            $sourceShippingMethod = $this->getShippingMethod($sourceCode);
            if (empty($sourceShippingMethod)) {
                continue;
            }
            $shippingRates[$sourceCode] = $this->getShippingRateByCode($sourceCode, $sourceShippingMethod);
        }
        $this->getShippingMethodWrapper()->setSourceCodes($sourceCodes);
        return $this->getShippingMethodWrapper()->filterSourceOptions($shippingRates);
    }

    /**
     * Generate shipping description
     * 
     * @param array|null $shippingMethods
     * @return void
     */
    public function generateShippingDescription($shippingMethods = null): void
    {
        $quoteAddress = $this->getObject();
        if ($shippingMethods === null) {
            $shippingMethods = $this->getShippingMethods();
        }
        $shippingRates = $this->getShippingRatesByCodes($shippingMethods);
        if (empty($shippingRates)) {
            $quoteAddress->setShippingDescription(null);
        }
        $shippingDescriptionPieces = [];
        foreach ($shippingRates as $sourceCode => $shippingRate) {
            $shippingCarrierTitle = $shippingRate->getCarrierTitle();
            $shippingMethodTitle = $shippingRate->getMethodTitle();
            $source = $this->getSourceBySourceCode->execute((string) $sourceCode);
            if (empty($source)) {
                continue;
            }
            $shippingDescriptionPieces[] = $source->getName().': '.$shippingCarrierTitle.($shippingMethodTitle ? ' - '.$shippingMethodTitle : '');
        }
        $quoteAddress->setShippingDescription(implode(', ', $shippingDescriptionPieces));
    }

    /**
     * Compare carriers
     *
     * @param array $firstItem
     * @param array $secondItem
     * @return int
     */
    protected function compareCarriers($firstItem, $secondItem): int
    {
        return $this->invokeMethod('_sortRates', $firstItem, $secondItem);
    }
    
    /**
     * Get store
     * 
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    protected function getStore(): \Magento\Store\Api\Data\StoreInterface
    {
        $quoteAddress = $this->getObject();
        $quote = $quoteAddress->getQuote();
        if (empty($quote) || !$quote->getStoreId()) {
            return $this->storeManager->getStore();
        }
        return $quote->getStore();
    }
    
    /**
     * Create shipping rate request
     * 
     * @return \Magento\Quote\Model\Quote\Address\RateRequest
     */
    protected function createShippingRateRequest(): \Magento\Quote\Model\Quote\Address\RateRequest
    {
        $quoteAddress = $this->getObject();
        $store = $this->getStore();
        $website = $store->getWebsite();
        $rateRequest = $this->getPropertyValue('_rateRequestFactory')->create();
        $rateRequest->setStoreId($store->getId());
        $rateRequest->setWebsiteId($website->getId());
        $rateRequest->setBaseCurrency($store->getBaseCurrency());
        $rateRequest->setPackageCurrency($store->getCurrentCurrency());
        $rateRequest->setDestCountryId($quoteAddress->getCountryId());
        $rateRequest->setDestRegionId($quoteAddress->getRegionId());
        $rateRequest->setDestRegionCode($quoteAddress->getRegionCode());
        $rateRequest->setDestStreet($quoteAddress->getStreetFull());
        $rateRequest->setDestCity($quoteAddress->getCity());
        $rateRequest->setDestPostcode($quoteAddress->getPostcode());
        return $rateRequest;
    }
    
    /**
     * Unset totals
     * 
     * @return void
     */
    protected function unsetTotals(): void
    {
        $this->setPropertyValue('_totals', []);
        $this->setPropertyValue('_totalAmounts', []);
        $this->setPropertyValue('_baseTotalAmounts', []);
    }

    /**
     * Get resource shipping method wrapper
     * 
     * @return \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\ResourceModel\Quote\Address\ShippingMethod
     */
    private function getResourceShippingMethodWrapper(): \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\ResourceModel\Quote\Address\ShippingMethod
    {
        return $this->wrapperFactory->create(
            $this->getObject()->getResource(),
            \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\ResourceModel\Quote\Address\ShippingMethod::class
        );
    }

    /**
     * Get limit carrier wrapper
     * 
     * @return \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\Address\LimitCarrier
     */
    private function getLimitCarrierWrapper(): \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\Address\LimitCarrier
    {
        $wrapper = $this->wrapperFactory->create(
            $this->getObject(),
            \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\Address\LimitCarrier::class
        );
        $wrapper->setSourceCodes($this->getSourceCodes());
        return $wrapper;
    }

    /**
     * Get shipping method wrapper
     * 
     * @return \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\Address\ShippingMethod
     */
    private function getShippingMethodWrapper(): \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\Address\ShippingMethod
    {
        $wrapper = $this->wrapperFactory->create(
            $this->getObject(),
            \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\Address\ShippingMethod::class
        );
        $wrapper->setSourceCodes($this->getSourceCodes());
        return $wrapper;
    }
    
    /**
     * Create item source clone
     * 
     * @param \Magento\Quote\Api\Data\CartItemInterface $item
     * @return \Magento\Quote\Api\Data\CartItemInterface
     */
    private function createItemSourceClone(\Magento\Quote\Api\Data\CartItemInterface $item): \Magento\Quote\Api\Data\CartItemInterface
    {
        $sourceItem = clone $item;
        $sourceItem->setExtensionAttributes(clone $item->getExtensionAttributes());
        $sourceItem->setId($item->getId());
        $sourceItem->setAddress($this->getObject());
        return $sourceItem;
    }

    /**
     * Filter source items
     * 
     * @return void
     */
    private function filterSourceItems(): void
    {
        $sourceCode = $this->getSourceCode();
        if (empty($sourceCode)) {
            return;
        }
        $quoteAddress = $this->getObject();
        $itemsCollection = $quoteAddress->getItemsCollection();
        $sourceItemsCollection = clone $itemsCollection;
        foreach ($itemsCollection as $itemKey => $item) {
            $sourceItemsCollection->removeItemByKey($itemKey);
        }
        foreach ($itemsCollection as $item) {
            $itemSourceCode = $this->quoteItemWrapperFactory->create($item)->getSourceCode();
            if ($itemSourceCode != $sourceCode || $item->getParentItem()) {
                continue;
            }
            $sourceItem = $this->createItemSourceClone($item);
            foreach ($item->getChildren() as $childItem) {
                $childItemSourceCode = $this->quoteItemWrapperFactory->create($childItem)->getSourceCode();
                if ($childItemSourceCode != $sourceCode) {
                    continue;
                }
                $sourceChildItem = $this->createItemSourceClone($childItem);
                $sourceChildItem->setParentItem($sourceItem);
                $sourceItemsCollection->addItem($sourceChildItem);
            }
            $sourceItemsCollection->addItem($sourceItem);
        }
        $quoteAddress->unsetData(['cached_items_all', 'item_qty']);
        $this->setPropertyValue('_items', $sourceItemsCollection);
    }

    

    /**
     * Create item shipping rate request
     * 
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return \Magento\Quote\Model\Quote\Address\RateRequest
     */
    private function createItemShippingRateRequest(\Magento\Quote\Model\Quote\Item\AbstractItem $item): \Magento\Quote\Model\Quote\Address\RateRequest
    {
        $quoteAddress = $this->getObject();
        $sourceCode = (string) $this->quoteItemWrapperFactory->create($item)->getSourceCode();
        $rateRequest = $this->createShippingRateRequest();
        $rateRequest->setSourceCode($sourceCode);
        $rateRequest->setAllItems([$item]);
        $rateRequest->setPackageValue($item->getBaseRowTotal());
        $rateRequest->setPackageValueWithDiscount($item->getBaseRowTotal() - $item->getBaseDiscountAmount());
        $rateRequest->setPackageWeight($item->getRowWeight());
        $rateRequest->setPackageQty($item->getQty());
        $rateRequest->setPackagePhysicalValue($item->getBaseRowTotal());
        $rateRequest->setBaseSubtotalInclTax($quoteAddress->getBaseSubtotalTotalInclTax());
        $rateRequest->setFreeMethodWeight(0);
        $rateRequest->setFreeShipping($quoteAddress->getFreeShipping());
        $rateRequest->setLimitCarrier($this->getLimitCarrier($sourceCode));
        return $rateRequest;
    }

    /**
     * Request item shipping rates
     * 
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return bool
     */
    private function requestItemShippingRates(\Magento\Quote\Model\Quote\Item\AbstractItem $item): bool
    {
        $found = false;
        $originSourceCode = $this->currentSourceProvider->getSourceCode();
        $sourceCode = (string) $this->quoteItemWrapperFactory->create($item)->getSourceCode();
        $this->currentSourceProvider->setSourceCode($sourceCode);
        $result = $this->getPropertyValue('_rateCollector')->create()
            ->collectRates($this->createItemShippingRateRequest($item))
            ->getResult();
        $this->currentSourceProvider->setSourceCode($originSourceCode);
        if (empty($result)) {
            return $found;
        }
        $addressRateFactory = $this->getPropertyValue('_addressRateFactory');
        foreach ($result->getAllRates() as $rateResult) {
            $rate = $addressRateFactory->create()->importShippingRate($rateResult);
            $rate->setSourceCode($sourceCode);
            if ($this->getShippingMethod($sourceCode) === $rate->getCode()) {
                $item->setBaseShippingAmount($rate->getPrice());
                $found = true;
            }
        }
        return $found;
    }

    /**
     * Create item shipping rate request
     * 
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @return \Magento\Quote\Model\Quote\Address\RateRequest
     */
    private function createAddressShippingRateRequest(\Magento\Quote\Api\Data\AddressInterface $address): \Magento\Quote\Model\Quote\Address\RateRequest
    {
        $quoteAddress = $this->getObject();
        $sourceCode = $address->getExtensionAttributes()->getSourceCode();
        $includeTax = $this->getPropertyValue('_scopeConfig')->getValue(
            'tax/calculation/price_includes_tax',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()->getId()
        );
        $baseSubtotal = $includeTax ? $address->getBaseSubtotalTotalInclTax() : $address->getBaseSubtotal();
        $baseSubtotalWithDiscount = $baseSubtotal + $address->getBaseDiscountAmount();
        $baseSubtotalWithoutVirtualAmount = $baseSubtotal - $quoteAddress->getBaseVirtualAmount();
        $rateRequest = $this->createShippingRateRequest();
        $rateRequest->setSourceCode($sourceCode);
        $rateRequest->setAllItems($address->getAllItems());
        $rateRequest->setPackageValue($baseSubtotal);
        $rateRequest->setPackageValueWithDiscount($baseSubtotalWithDiscount);
        $rateRequest->setPackageWeight($address->getWeight());
        $rateRequest->setPackageQty($address->getItemQty());
        $rateRequest->setPackagePhysicalValue($baseSubtotalWithoutVirtualAmount);
        $rateRequest->setBaseSubtotalInclTax($address->getBaseSubtotalTotalInclTax());
        $rateRequest->setFreeMethodWeight($address->getFreeMethodWeight());
        $rateRequest->setFreeShipping($address->getFreeShipping());
        $this->setObject($address);
        $rateRequest->setLimitCarrier($this->getLimitCarrier($sourceCode));
        $this->setObject($quoteAddress);
        return $rateRequest;
    }

    /**
     * Request address shipping rates
     * 
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @return bool
     */
    private function requestAddressShippingRates(\Magento\Quote\Api\Data\AddressInterface $address): bool
    {
        $quoteAddress = $this->getObject();
        $found = false;
        $originSourceCode = $this->currentSourceProvider->getSourceCode();
        $sourceCode = $address->getExtensionAttributes()->getSourceCode();
        $this->currentSourceProvider->setSourceCode($sourceCode);
        $result = $this->getPropertyValue('_rateCollector')->create()
            ->collectRates($this->createAddressShippingRateRequest($address))
            ->getResult();
        $this->currentSourceProvider->setSourceCode($originSourceCode);
        if (empty($result)) {
            return $found;
        }
        $store = $this->getStore();
        $baseCurrency = $store->getBaseCurrency();
        $currentCurrencyCode = $store->getCurrentCurrencyCode();
        foreach ($result->getAllRates() as $shippingRate) {
            $rate = $this->getPropertyValue('_addressRateFactory')->create()->importShippingRate($shippingRate);
            $rate->setSourceCode($sourceCode);
            $quoteAddress->addShippingRate($rate);
            if ($this->getShippingMethod($sourceCode) === $rate->getCode()) {
                $baseShippingAmount = $rate->getPrice();
                $shippingAmount = $baseCurrency->convert($baseShippingAmount, $currentCurrencyCode);
                $quoteAddress->setBaseShippingAmount($quoteAddress->getBaseShippingAmount() + $baseShippingAmount);
                $quoteAddress->setShippingAmount($quoteAddress->getShippingAmount() + $shippingAmount);
                $found = true;
            }
        }
        return $found;
    }
}