<?php

namespace Amasty\GiftCardGraphQl\Model\Resolver;

use Amasty\GiftCardAccount\Api\GiftCardAccountManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;

class RemoveGiftCardFromCart implements ResolverInterface
{
    const CART_ID_KEY = 'cart_id';
    const GIFT_CARD_CODE_KEY = 'am_gift_card_code';

    /**
     * @var GetCartForUser
     */
    private $getCartForUser;

    /**
     * @var GiftCardAccountManagementInterface
     */
    private $giftCardAccountManagement;

    public function __construct(
        GetCartForUser $getCartForUser,
        GiftCardAccountManagementInterface $giftCardAccountManagement
    ) {
        $this->getCartForUser = $getCartForUser;
        $this->giftCardAccountManagement = $giftCardAccountManagement;
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
        if (!isset($args['input'][self::CART_ID_KEY]) || empty($args['input'][self::CART_ID_KEY])) {
            throw new GraphQlInputException(__('Required parameter "%1" is missing', self::CART_ID_KEY));
        }

        if (!isset($args['input'][self::GIFT_CARD_CODE_KEY]) || empty($args['input'][self::GIFT_CARD_CODE_KEY])) {
            throw new GraphQlInputException(__('Required parameter "%1" is missing', self::GIFT_CARD_CODE_KEY));
        }
        $giftCardCode = $args['input'][self::GIFT_CARD_CODE_KEY];
        $maskedCartId = $args['input'][self::CART_ID_KEY];

        $currentUserId = $context->getUserId();
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
        $cart = $this->getCartForUser->execute($maskedCartId, $currentUserId, $storeId);

        try {
            $this->giftCardAccountManagement->removeGiftCardFromCart($cart->getId(), $giftCardCode);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()), $e);
        }

        return [
            'cart' => [
                'model' => $cart,
            ],
        ];
    }
}
