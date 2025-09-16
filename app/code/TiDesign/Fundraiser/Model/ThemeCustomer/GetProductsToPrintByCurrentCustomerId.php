<?php

namespace TiDesign\Fundraiser\Model\ThemeCustomer;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use TiDesign\Fundraiser\Helper\Config as ConfigHelper;
use Magento\Eav\Model\Config as EavConfig;

class GetProductsToPrintByCurrentCustomerId
{
    /**
     * @param GetProductCollectionByCurrentCustomerId $getProductCollectionByCurrentCustomerId
     * @param ConfigHelper $configHelper
     * @param EavConfig $eavConfig
     */
    public function __construct(
        protected GetProductCollectionByCurrentCustomerId $getProductCollectionByCurrentCustomerId,
        protected ConfigHelper $configHelper,
        protected EavConfig $eavConfig
    ) {
    }

    /**
     * @param int $themeId
     * @return \Magento\Catalog\Model\Product[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute($themeId)
    {
        $results = [];
        $sizeAttributeCode = $this->configHelper->getProductSizeAttributeCode();
        $sizeAttribute = $this->eavConfig->getAttribute('catalog_product', $sizeAttributeCode);
        $productCollection = $this->getProductCollectionByCurrentCustomerId->execute($themeId);
        $productsInPage = [];
        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($productCollection->getItems() as $product) {
            $productSises = $this->getProductSizes($product, $sizeAttribute);
            $product->setData('FUNDRAISER_PRODUCT_SIZE_AVAILABILITIES', $productSises);
            $productsInPage[] = $product;
            if (count($productsInPage) >= 6) {
                $results[] = $productsInPage;
                $productsInPage = [];
            }
        }
        return $results;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $sizeAttribute
     * @return array
     */
    private function getProductSizes($product, $sizeAttribute)
    {
        if ($product->getTypeId() === Configurable::TYPE_CODE) {
            return $this->getConfigurableSizes($product, $sizeAttribute);
        } else {
            $sizeAttributeCode = $sizeAttribute->getAttributeCode();
            $isSalable = $product->isSalable();
            $size = $product->getData($sizeAttributeCode) ?: "";
            $attributeOptions = $this->getAttributeOptions($sizeAttribute);
            $optionTitle = $attributeOptions[$size] ?? "";
            return [$sizeAttributeCode => [$size => ['option_title' => $optionTitle, 'is_salable' => $isSalable]]];
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $sizeAttribute
     * @return array
     */
    private function getConfigurableSizes($product, $sizeAttribute)
    {
        $sizeAttributeCode = $sizeAttribute->getAttributeCode();
        $configurableOptions = $product->getTypeInstance()->getConfigurableOptions($product);
        $usedOptions = array_reduce($configurableOptions, function ($carry, $options) use ($sizeAttributeCode) {
            return array_reduce($options, function ($carry, $option) use ($sizeAttributeCode) {
                if ($option['attribute_code'] === $sizeAttributeCode) {
                    $carry[$sizeAttributeCode][$option['value_index']] = [
                        'option_title' => $option['option_title']
                    ];
                }
                return $carry;
            }, $carry);
        }, []);
        $salableProducts = $product->getTypeInstance()->getUsedProducts($product, [$sizeAttributeCode]);
        return array_reduce($salableProducts, function ($carry, $product) use ($sizeAttributeCode) {
            /** @var \Magento\Catalog\Model\Product $product */
            $isSalable = $product->isSalable();
            $size = $product->getData($sizeAttributeCode) ?: "";
            $isSalable = $isSalable ?: ($carry[$sizeAttributeCode][$size]['is_salable'] ?? false);
            $carry[$sizeAttributeCode][$size]['option_title'] = $carry[$sizeAttributeCode][$size]['option_title'] ?? "";
            $carry[$sizeAttributeCode][$size]['is_salable'] = $isSalable;
            return $carry;
        }, $usedOptions);
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @return array
     */
    private function getAttributeOptions($attribute)
    {
        if ($attribute->hasData('options_by_option_id')) {
            return $attribute->getData('options_by_option_id');
        }
        $options = $attribute->getOptions();
        $options = array_reduce($options, function ($carry, $option) {
            if ($option->getValue() && $option->getLabel()) {
                $carry[$option->getValue()] = $option->getLabel();
            }
        }, []);
        $attribute->setData('options_by_option_id', $options);
        return $options;
    }
}

