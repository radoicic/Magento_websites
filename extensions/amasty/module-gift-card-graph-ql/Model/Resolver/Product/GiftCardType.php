<?php
declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Model\Resolver\Product;

use Amasty\GiftCard\Model\GiftCard\Product\Type\GiftCard;
use Magento\Catalog\Model\Product;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\EnumLookup;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class GiftCardType implements ResolverInterface
{
    /**
     * @var EnumLookup
     */
    private $enumLookup;

    public function __construct(EnumLookup $enumLookup)
    {
        $this->enumLookup = $enumLookup;
    }

    /**
     * @inheritDoc
     */
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

        if ($product->getTypeId() === GiftCard::TYPE_AMGIFTCARD) {
            $data = $this->enumLookup->getEnumValueFromField(
                'AmGiftCardTypeEnum',
                $product->getAmGiftcardType()
            );
        }

        return $data ?? null;
    }
}
