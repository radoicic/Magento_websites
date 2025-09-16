<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Pricing\Bundle\Price;

/**
 * Bundle selection price plugin
 */
class BundleSelectionPrice extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Around get value
     * 
     * @param \Magento\Bundle\Pricing\Price\BundleSelectionPrice $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundGetValue(
        \Magento\Bundle\Pricing\Price\BundleSelectionPrice $subject,
        \Closure $proceed
    )
    {
        $this->setSubject($subject);
        $value = $this->getSubjectPropertyValue('value');
        if (null !== $value) {
            return $value;
        }
        $selectionKey = $this->getSelectionKey('value');
        $bundleProduct = $this->getSubjectPropertyValue('bundleProduct');
        if ($bundleProduct->hasData($selectionKey)) {
            return $bundleProduct->getData($selectionKey);
        }
        $useRegularPrice = $this->getSubjectPropertyValue('useRegularPrice');
        $priceCode = $useRegularPrice ? \Magento\Bundle\Pricing\Price\BundleRegularPrice::PRICE_CODE : \Magento\Bundle\Pricing\Price\FinalPrice::PRICE_CODE;
        if ($bundleProduct->getPriceType() == \Magento\Bundle\Model\Product\Price::PRICE_TYPE_DYNAMIC) {
            $value = $this->getSubjectPropertyValue('priceInfo')->getPrice($priceCode)->getValue();
        } else {
            $selection = $this->getSubjectPropertyValue('selection');
            $selectionPriceValue = $selection->getSelectionPriceValue();
            if ($selection->getSelectionPriceType()) {
                $price = $bundleProduct->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE)->getValue();
                $product = clone $bundleProduct;
                $product->setFinalPrice($price);
                $this->getSubjectPropertyValue('eventManager')->dispatch(
                    'catalog_product_get_final_price',
                    [
                        'product' => $product,
                        'qty' => $bundleProduct->getQty(),
                    ]
                );
                $value = $useRegularPrice ? $product->getData('price') : $product->getData('final_price') * ($selectionPriceValue / 100);
            } else {
                $value = $this->getSubjectPropertyValue('priceCurrency')->convert($selectionPriceValue);
            }
        }
        if (!$useRegularPrice) {
            $value = $this->getSubjectPropertyValue('discountCalculator')->calculateDiscount($bundleProduct, $value);
        }
        $value = $this->getSubjectPropertyValue('priceCurrency')->round($value);
        $this->setSubjectPropertyValue('value', $value);
        $bundleProduct->setData($selectionKey, $value);
        return $value;
    }

    /**
     * Around get amount
     * 
     * @param \Magento\Bundle\Pricing\Price\BundleSelectionPrice $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundGetAmount(
        \Magento\Bundle\Pricing\Price\BundleSelectionPrice $subject,
        \Closure $proceed
    )
    {
        $this->setSubject($subject);
        $value = $subject->getValue();
        $amount = $this->getSubjectPropertyValue('amount');
        if (isset($amount[$value])) {
            return $amount[$value];
        }
        $selectionKey = $this->getSelectionKey('amount');
        $bundleProduct = $this->getSubjectPropertyValue('bundleProduct');
        if ($bundleProduct->hasData($selectionKey)) {
            return $bundleProduct->getData($selectionKey);
        }
        $exclude = null;
        if ($subject->getProduct()->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            $exclude = $this->getSubjectPropertyValue('excludeAdjustment');
        }
        $amount[$value] = $this->getSubjectPropertyValue('calculator')->getAmount($value, $subject->getProduct(), $exclude);
        $this->setSubjectPropertyValue('amount', $amount);
        $bundleProduct->setData($selectionKey, $amount[$value]);
        return $amount[$value];
    }

    /**
     * Get selection key
     * 
     * @param string $field
     * @return string
     */
    private function getSelectionKey(string $field): string
    {
        $selection = $this->getSubjectPropertyValue('selection');
        $keyParts = ['bundle-selection'];
        if ($this->getSubjectPropertyValue('useRegularPrice')) {
            $keyParts[] = 'regular';
        }
        $keyParts[] = $field;
        $keyParts[] = $selection->getSelectionId();
        $keyParts[] = $selection->getSourceCode();
        return implode('-', $keyParts);
    }
}