<?php

declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Model\Resolver;

use Amasty\GiftCard\Model\GiftCard\CustomerData\GiftCardItem;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class GiftCardCartImage implements ResolverInterface
{
    /**
     * @var GiftCardItem
     */
    private $giftCardItem;

    public function __construct(GiftCardItem $giftCardItem)
    {
        $this->giftCardItem = $giftCardItem;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['model'])) {
            throw new GraphQlInputException(__('"model" value should be specified'));
        }
        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        $quoteItem = $value['model'];

        return $this->giftCardItem->getItemImageUrl($quoteItem);
    }
}
