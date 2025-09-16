<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Product Labels for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Label\Model\Source\Rules\Operator;

use Magento\Framework\Data\OptionSourceInterface;

class OnSale implements OptionSourceInterface
{
    public const FOR_SPECIAL_PRICE_ONLY = '<=>';
    public const EQUAL = '==';
    public const NOT_EQUAL = '!=';

    public function toOptionArray(): array
    {
        return [
            [
                'label' => __('is'),
                'value' => self::EQUAL
            ],
            [
                'label' => __('is not'),
                'value' => self::NOT_EQUAL
            ],
            [
                'label' => __('for special price only'),
                'value' => self::FOR_SPECIAL_PRICE_ONLY
            ]
        ];
    }

    public function toArray(): array
    {
        $options = $this->toOptionArray();

        return array_combine(
            array_column($options, 'value'),
            array_column($options, 'label')
        );
    }
}
