<?php
declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Model\Resolver\Product;

use Amasty\GiftCard\Model\GiftCard\Product\Type\GiftCard;
use Magento\Framework\GraphQl\Query\Resolver\TypeResolverInterface;

class GiftCardProductTypeResolver implements TypeResolverInterface
{
    /**
     * @inheritDoc
     */
    public function resolveType(array $data) : string
    {
        if (isset($data['type_id']) && $data['type_id'] == GiftCard::TYPE_AMGIFTCARD) {
            $productType = 'AmGiftCardProduct';
        }

        return $productType ?? '';
    }
}
