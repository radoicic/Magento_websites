<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Block\ConfigurableProduct\Product\View\Type;

/**
 * Configurable view product block plugin
 */
class Configurable
{
    /**
     * Get current salable source items
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetCurrentSalableSourceItems
     */
    private $getCurrentSalableSourceItems;
    
    /**
     * Locale format
     * 
     * @var \Magento\Framework\Locale\Format
     */
    private $localeFormat;
    
    /**
     * Serializer
     * 
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetCurrentSalableSourceItems $getCurrentSalableSourceItems
     * @param \Magento\Framework\Locale\Format $localeFormat
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetCurrentSalableSourceItems $getCurrentSalableSourceItems,
        \Magento\Framework\Locale\Format $localeFormat,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    )
    {
        $this->getCurrentSalableSourceItems = $getCurrentSalableSourceItems;
        $this->localeFormat = $localeFormat;
        $this->serializer = $serializer;
    }
    
    /**
     * After get JSON configuration
     * 
     * @param \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject
     * @param string $result
     * @return string
     */
    public function afterGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        $result
    )
    {
        $config = $this->serializer->unserialize($result);
        $containerProduct = $subject->getProduct();
        $sourceCodes = array_keys($this->getCurrentSalableSourceItems->execute($containerProduct->getData(\Magento\Catalog\Api\Data\ProductInterface::SKU)));
        foreach ($subject->getAllowProducts() as $product) {
            $initialSourceCode = $product->getSourceCode();
            foreach ($sourceCodes as $sourceCode) {
                $config['sourceOptionPrices'][(string) $sourceCode][$product->getId()] = $this->getSourceOptionPricesData($product, (string) $sourceCode);
            }
            $this->setProductSourceCode($product, $initialSourceCode);
        }
        return $this->serializer->serialize($config);
    }
    
    /**
     * Set product source code
     * 
     * @param \Magento\Catalog\Model\Product $product
     * @param string|null $sourceCode
     * @return void
     */
    private function setProductSourceCode(\Magento\Catalog\Model\Product $product, string $sourceCode = null): void
    {
        $product->setSourceCode($sourceCode);
        $product->reloadPriceInfo();
    }
    
    /**
     * Get source option tier prices data
     * 
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    private function getSourceOptionTierPricesData($product): array
    {
        $data = [];
        $tierPrice = $product->getPriceInfo()->getPrice('tier_price');
        foreach ($tierPrice->getTierPriceList() as $tierPrice) {
            $data[] = [
                'qty' => $this->localeFormat->getNumber($tierPrice['price_qty']),
                'price' => $this->localeFormat->getNumber($tierPrice['price']->getValue()),
                'percentage' => $this->localeFormat->getNumber($tierPrice->getSavePercent($tierPrice['price'])),
            ];
        }
        return $data;
    }
    
    /**
     * Get source option prices data
     * 
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    private function getSourceOptionPricesData($product, $sourceCode): array
    {
        $this->setProductSourceCode($product, $sourceCode);
        $priceInfo = $product->getPriceInfo();
        $regularPriceAmount = $priceInfo->getPrice('regular_price')->getAmount();
        $finalPriceAmount = $priceInfo->getPrice('final_price')->getAmount();
        return [
            'oldPrice' => [
                'amount' => $regularPriceAmount->getValue(),
            ],
            'basePrice' => [
                'amount' => $finalPriceAmount->getBaseAmount(),
            ],
            'finalPrice' => [
                'amount' => $finalPriceAmount->getValue(),
            ],
            'tierPrices' => $this->getSourceOptionTierPricesData($product),
        ];
    }
}