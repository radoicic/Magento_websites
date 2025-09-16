<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Catalog\Product\Type;

/**
 * Abstract product type model plugin
 */
class AbstractType
{
    /**
     * Serializer
     * 
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @return void
     */
    public function __construct(\Magento\Framework\Serialize\Serializer\Json $serializer)
    {
        $this->serializer = $serializer;
    }
    
    /**
     * Around process configuration
     * 
     * @param \Magento\Catalog\Model\Product\Type\AbstractType $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\DataObject $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param string $processMode
     * @return array|string
     */
    public function aroundProcessConfiguration(
        \Magento\Catalog\Model\Product\Type\AbstractType $subject,
        \Closure $proceed,
        \Magento\Framework\DataObject $buyRequest,
        $product,
        $processMode = \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_LITE
    )
    {
        return $this->prepareProduct($proceed($buyRequest, $product, $processMode), $buyRequest, $product);
    }
    
    /**
     * Around prepare for cart advanced
     * 
     * @param \Magento\Catalog\Model\Product\Type\AbstractType $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\DataObject $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param null|string $processMode
     * @return array|string
     */
    public function aroundPrepareForCartAdvanced(
        \Magento\Catalog\Model\Product\Type\AbstractType $subject,
        \Closure $proceed,
        \Magento\Framework\DataObject $buyRequest,
        $product,
        $processMode = null
    )
    {
        return $this->prepareProduct($proceed($buyRequest, $product, $processMode), $buyRequest, $product);
    }
    
    /**
     * Around check product buy state
     * 
     * @param \Magento\Catalog\Model\Product\Type\AbstractType $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product\Type\AbstractType
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundCheckProductBuyState(
        \Magento\Catalog\Model\Product\Type\AbstractType $subject,
        \Closure $proceed,
        $product
    )
    {
        $proceed($product);
        $option = $product->getCustomOption('info_buyRequest');
        if (empty($option) || !($option instanceof \Magento\Quote\Model\Quote\Item\Option)) {
            return $this;
        }
        $buyRequest = new \Magento\Framework\DataObject($this->serializer->unserialize($option->getValue()));
        $sourceCode = (string) $buyRequest->getSource();
        if (empty($sourceCode)) {
            throw new \Magento\Framework\Exception\LocalizedException($this->getSpecifyOptionMessage());
        }
        return $subject;
    }
    
    /**
     * Can configure
     * 
     * @param \Magento\Catalog\Model\Product\Type\AbstractType $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function aroundCanConfigure(
        \Magento\Catalog\Model\Product\Type\AbstractType $subject,
        \Closure $proceed,
        $product
    )
    {
        return true;
    }
    
    /**
     * Around has options
     * 
     * @param \Magento\Catalog\Model\Product\Type\AbstractType $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function aroundHasOptions(
        \Magento\Catalog\Model\Product\Type\AbstractType $subject,
        \Closure $proceed,
        $product
    )
    {
        if ($this->isGroupedProduct($product)) {
            return $proceed($product);
        }
        return true;
    }
    
    /**
     * Around has required options
     * 
     * @param \Magento\Catalog\Model\Product\Type\AbstractType $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function aroundHasRequiredOptions(
        \Magento\Catalog\Model\Product\Type\AbstractType $subject,
        \Closure $proceed,
        $product
    )
    {
        return true;
    }
    
    /**
     * Around process buy request
     * 
     * @param \Magento\Catalog\Model\Product\Type\AbstractType $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\DataObject $buyRequest
     * @return array
     */
    public function aroundProcessBuyRequest(
        \Magento\Catalog\Model\Product\Type\AbstractType $subject,
        \Closure $proceed,
        $product,
        $buyRequest
    )
    {
        $options = $proceed($product, $buyRequest);
        if (!is_array($options)) {
            $options = [];
        }
        $options['source'] = $buyRequest->getSource();
        return $options;
    }
    
    /**
     * Get specify option message
     *
     * @return \Magento\Framework\Phrase
     */
    private function getSpecifyOptionMessage(): \Magento\Framework\Phrase
    {
        return __('Please specify the product source.');
    }
    
    /**
     * Check if is grouped product
     * 
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    private function isGroupedProduct($product): bool
    {
        return $product->getTypeId() === \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE;
    }
    
    /**
     * Prepare product
     * 
     * @param array|string $products
     * @param \Magento\Framework\DataObject $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @return array|string
     */
    private function prepareProduct($products, \Magento\Framework\DataObject $buyRequest, $product)
    {
        if (is_string($products)) {
            return $products;
        }
        $sourceCode = (string) $buyRequest->getSource();
        if (empty($sourceCode)) {
            if (!empty($buyRequest->getQty())) {
                return (string) $this->getSpecifyOptionMessage();
            } else {
                return $products;
            }
        }
        $product->addCustomOption('source', $sourceCode);
        if (count($products)) {
            foreach ($products as $childProduct) {
                $childProduct->addCustomOption('source', $sourceCode);
                $childOption = $childProduct->getCustomOption('info_buyRequest');
                $childOptionData = $this->serializer->unserialize($childOption->getValue());
                if (!is_array($childOptionData)) {
                    $childOptionData = [];
                }
                $childOptionData['source'] = $sourceCode;
                $childProduct->addCustomOption('info_buyRequest', $this->serializer->serialize($childOptionData));
            }
        }
        return $products;
    }
}