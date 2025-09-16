<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Pricing\Bundle\Adjustment;

/**
 * Adjustment calculator plugin
 */
class Calculator extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Option amount
     * 
     * @var array
     */
    private $optionAmount = [];

    /**
     * Around get options amount
     * 
     * @param \Magento\Bundle\Pricing\Adjustment\Calculator $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $saleableItem
     * @param null|bool|string|array $exclude
     * @param bool $searchMin
     * @param float $baseAmount
     * @param bool $useRegularPrice
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function aroundGetOptionsAmount(
        \Magento\Bundle\Pricing\Adjustment\Calculator $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $saleableItem,
        $exclude = null,
        $searchMin = true,
        $baseAmount = 0.,
        $useRegularPrice = false
    )
    {
        $this->setSubject($subject);
        $sourceCode = (string) $saleableItem->getSourceCode();
        $cacheKey = implode('-', [ $saleableItem->getId(), $sourceCode, $exclude, $searchMin, $baseAmount, $useRegularPrice ]);
        if (!isset($this->optionAmount[$cacheKey])) {
            $this->optionAmount[$cacheKey] = $subject->calculateBundleAmount(
                $baseAmount,
                $saleableItem,
                $this->invokeSubjectMethod('getSelectionAmounts', $saleableItem, $searchMin, $useRegularPrice),
                $exclude
            );
        }
        return $this->optionAmount[$cacheKey];
    }
}