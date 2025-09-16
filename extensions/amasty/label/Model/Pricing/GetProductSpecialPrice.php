<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Product Labels for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Label\Model\Pricing;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Catalog\Pricing\Price\RegularPrice;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class GetProductSpecialPrice
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(
        TimezoneInterface $timezone
    ) {
        $this->timezone = $timezone;
    }

    public function execute(ProductInterface $product): float
    {
        $specialFromDate = $product->getSpecialFromDate();
        $specialToDate = $product->getSpecialToDate();
        $isSpecialPriceValid = $specialFromDate || $specialToDate
            ? $this->timezone->isScopeDateInInterval($product->getStoreId(), $specialFromDate, $specialToDate)
            : false;

        if ($product->getTypeId() !== Configurable::TYPE_CODE && $isSpecialPriceValid) {
            $specialPrice = $this->getPrice($product, ProductAttributeInterface::CODE_SPECIAL_PRICE);
            $specialPrice = $specialPrice !== false
                ? $specialPrice
                : $this->getPrice($product, RegularPrice::PRICE_CODE);
        } else {
            $specialPrice = $this->getPrice($product, FinalPrice::PRICE_CODE);
        }

        return (float) $specialPrice;
    }

    /**
     * @param ProductInterface $product
     * @param string $code
     *
     * @return float|string|false
     */
    private function getPrice(ProductInterface $product, string $code)
    {
        return $product->getPriceInfo()->getPrice($code)->getAmount()->getValue();
    }
}
