<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Block\Bundle\Catalog\Product\View\Type;

/**
 * Bundle view product block plugin
 */
class Bundle
{
    /**
     * Get current salable source items
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetCurrentSalableSourceItems
     */
    private $getCurrentSalableSourceItems;
    
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
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySales\GetCurrentSalableSourceItems $getCurrentSalableSourceItems,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    )
    {
        $this->getCurrentSalableSourceItems = $getCurrentSalableSourceItems;
        $this->serializer = $serializer;
    }

    /**
     * After get JSON configuration
     * 
     * @param \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle $subject
     * @param string $result
     * @return string
     */
    public function afterGetJsonConfig(
        \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle $subject,
        $result
    )
    {
        $config = $this->serializer->unserialize($result);
        $product = $subject->getProduct();
        $sourceCodes = array_keys($this->getCurrentSalableSourceItems->execute($product->getData(\Magento\Catalog\Api\Data\ProductInterface::SKU)));
        $config = $this->setOptionsSourcesPricesData($product, $subject->getOptions(), $config, $sourceCodes);
        $config = $this->setSourcesPricesData($product, $config, $sourceCodes);
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
     * Get option selection source prices data
     * 
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Product $selection
     * @param string $sourceCode
     * @return array
     */
    private function getOptionSelectionSourcePricesData(
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Model\Product $selection,
        $sourceCode
    ): array
    {
        $this->setProductSourceCode($product, (string) $sourceCode);
        $this->setProductSourceCode($selection, (string) $sourceCode);
        $productPriceInfo = $product->getPriceInfo();
        $optionPriceAmount = $productPriceInfo->getPrice(\Magento\Bundle\Pricing\Price\BundleOptionPrice::PRICE_CODE)
            ->getOptionSelectionAmount($selection);
        $optionRegularPriceAmount = $productPriceInfo->getPrice(\Magento\Bundle\Pricing\Price\BundleOptionRegularPrice::PRICE_CODE)
            ->getOptionSelectionAmount($selection);
        return [
            'oldPrice' => [
                'amount' => $optionRegularPriceAmount->getValue(),
            ],
            'basePrice' => [
                'amount' => $optionPriceAmount->getBaseAmount(),
            ],
            'finalPrice' => [
                'amount' => $optionPriceAmount->getValue(),
            ],
        ];
    }

    /**
     * Set options sources prices data
     * 
     * @param \Magento\Catalog\Model\Product $product
     * @param array $options
     * @param array $config
     * @param array $sourceCodes
     * @return array
     */
    private function setOptionsSourcesPricesData(\Magento\Catalog\Model\Product $product, $options, $config, $sourceCodes): array
    {
        foreach ($options as $option) {
            $optionId = $option->getId();
            foreach ($option->getSelections() as $selection) {
                $selectionId = $selection->getSelectionId();
                $initialSourceCode = $product->getSourceCode();
                $config['options'][$optionId]['selections'][$selectionId]['sourcePrices'] = [];
                foreach ($sourceCodes as $sourceCode) {
                    $config['options'][$optionId]['selections'][$selectionId]['sourcePrices'][$sourceCode] = $this->getOptionSelectionSourcePricesData(
                        $product,
                        $selection,
                        (string) $sourceCode
                    );
                }
                $this->setProductSourceCode($product, $initialSourceCode);
                $this->setProductSourceCode($selection, $initialSourceCode);
            }
        }
        return $config;
    }

    /**
     * Get source prices data
     * 
     * @param \Magento\Catalog\Model\Product $product
     * @param string $sourceCode
     * @return array
     */
    private function getSourcePricesData(\Magento\Catalog\Model\Product $product, $sourceCode): array
    {
        $this->setProductSourceCode($product, (string) $sourceCode);
        $priceInfo = $product->getPriceInfo();
        $finalPriceAmount = $priceInfo->getPrice(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE)
            ->getPriceWithoutOption();
        $regularPriceAmount = $priceInfo->getPrice(\Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE)
            ->getAmount();
        $isFixedPrice = $product->getPriceType() == \Magento\Bundle\Model\Product\Price::PRICE_TYPE_FIXED;
        return [
            'oldPrice' => [
                'amount' => $isFixedPrice ? $regularPriceAmount->getValue() : 0
            ],
            'basePrice' => [
                'amount' => $isFixedPrice ? $finalPriceAmount->getBaseAmount() : 0
            ],
            'finalPrice' => [
                'amount' => $isFixedPrice ? $finalPriceAmount->getValue() : 0
            ],
        ];
    }
    
    /**
     * Set sources prices data
     * 
     * @param \Magento\Catalog\Model\Product $product
     * @param array $config
     * @param array $sourceCodes
     * @return array
     */
    private function setSourcesPricesData(\Magento\Catalog\Model\Product $product, $config, $sourceCodes): array
    {
        $initialSourceCode = $product->getSourceCode();
        $config['sourcePrices'] = [];
        foreach ($sourceCodes as $sourceCode) {
            $config['sourcePrices'][$sourceCode] = $this->getSourcePricesData($product, (string) $sourceCode);
        }
        $this->setProductSourceCode($product, $initialSourceCode);
        return $config;
    }
}