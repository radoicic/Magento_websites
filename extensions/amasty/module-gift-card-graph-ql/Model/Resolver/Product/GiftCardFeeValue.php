<?php
declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Model\Resolver\Product;

use Amasty\GiftCard\Model\Config\Source\Fee;
use Amasty\GiftCard\Model\GiftCard\Product\Type\GiftCard;
use Magento\Catalog\Model\Product;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class GiftCardFeeValue implements ResolverInterface
{
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($value['model'])) {
            throw new GraphQlInputException(__('"model" value should be specified'));
        }

        /** @var Product $product */
        $product = $value['model'];
        $result = 0;

        if ($product->getTypeId() === GiftCard::TYPE_AMGIFTCARD) {
            $feeType = (int)$product->getAmGiftcardFeeType();
            $feeValue = $value['am_giftcard_fee_value'] ?? 0;
            if ($feeType === Fee::PRICE_TYPE_PERCENT) {
                $result = $feeValue;
            } elseif ($feeType === Fee::PRICE_TYPE_FIXED) {
                $store = $context->getExtensionAttributes()->getStore();
                $result = $store->getBaseCurrency()->convert(
                    $feeValue,
                    $store->getCurrentCurrency()
                );
            }
        }

        return $result;
    }
}
