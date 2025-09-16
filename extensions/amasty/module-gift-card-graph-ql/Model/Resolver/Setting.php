<?php

declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Model\Resolver;

use Amasty\GiftCard\Model\ConfigProvider;
use Magento\Config\Model\Config\Source\Locale\Timezone;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Setting implements ResolverInterface
{
    /**
     * @var Timezone
     */
    private $timezone;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Timezone $timezone,
        ConfigProvider $configProvider
    ) {
        $this->timezone = $timezone;
        $this->configProvider = $configProvider;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $timezoneCodes = $this->configProvider->getGiftCardTimezone();
        $timezones = $this->timezone->toOptionArray();

        if ($timezoneCodes) {
            $timezones = array_filter($timezones, function ($timezoneData) use ($timezoneCodes) {
                return in_array($timezoneData['value'], $timezoneCodes);
            });
        }

        return [
            'isEnabled' => $this->configProvider->isEnabled(),
            'allowedProductTypes' => $this->configProvider->getAllowedProductTypes(),
            'isSippingPaidAllowed' => $this->configProvider->isShippingPaidAllowed(),
            'isTaxPaidAllowed' => $this->configProvider->isTaxPaidAllowed(),
            'isExtraFeePaidAllowed' => $this->configProvider->isExtraFeePaidAllowed(),
            'giftCardFields' => $this->configProvider->getGiftCardFields(),
            'isShowOnCartAndCheckout' => $this->configProvider->isShowOptionsInCartAndCheckout(),
            'giftCardTimezones' => $timezones,
            'isAllowUserImages' => $this->configProvider->isAllowUserImages(),
            'imageUploadTooltip' => $this->configProvider->getImageUploadTooltip(),
            'giftCardLifetime' => $this->configProvider->getLifetime(),
            'isAllowUseThemselves' => $this->configProvider->isAllowUseThemselves()
        ];
    }
}
