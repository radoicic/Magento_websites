<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Pricing\Bundle\Price;

/**
 * Bundle options plugin
 */
class BundleOptions extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Option selection amount cache
     * 
     * @var array
     */
    private $optionSelectionAmountCache = [];

    /**
     * Around get option selection amount
     * 
     * @param \Magento\Bundle\Pricing\Price\BundleOptions $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $bundleProduct
     * @param \Magento\Bundle\Model\Selection|\Magento\Catalog\Model\Product $selection
     * @param bool $useRegularPrice
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function aroundGetOptionSelectionAmount(
        \Magento\Bundle\Pricing\Price\BundleOptions $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $bundleProduct,
        $selection,
        bool $useRegularPrice = false
    ): \Magento\Framework\Pricing\Amount\AmountInterface
    {
        $this->setSubject($subject);
        $optionSelectionAmountKey = implode('_', [
            $bundleProduct->getId(),
            $selection->getOptionId(),
            $selection->getSelectionId(),
            $selection->getSourceCode(),
            $useRegularPrice ? 1 : 0,
        ]);
        if (!isset($this->optionSelectionAmountCache[$optionSelectionAmountKey])) {
            $selectionPrice = $this->getSubjectPropertyValue('selectionFactory')->create(
                $bundleProduct,
                $selection,
                $selection->getSelectionQty(),
                [
                    'useRegularPrice' => $useRegularPrice,
                ]
            );
            $this->optionSelectionAmountCache[$optionSelectionAmountKey] =  $selectionPrice->getAmount();
        }
        return $this->optionSelectionAmountCache[$optionSelectionAmountKey];
    }
}