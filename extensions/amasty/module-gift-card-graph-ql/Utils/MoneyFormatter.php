<?php
declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Utils;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;

class MoneyFormatter
{
    /**
     * Convert value into a Money type array
     *
     * @param float|string $value
     * @param Store $store
     * @throws LocalizedException
     * @return array
     */
    public function format($value, Store $store): array
    {
        $currentCurrency = $store->getCurrentCurrency();
        $baseCurrency = $store->getBaseCurrency();

        return [
            'value' => $baseCurrency->convert($value, $currentCurrency),
            'currency' => $currentCurrency->getCode()
        ];
    }
}
