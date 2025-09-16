<?php
declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Model\Resolver;

use Amasty\GiftCardAccount\Api\GiftCardAccountManagementInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;

class ApplyGiftCardToCart implements ResolverInterface
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

    /**
     * @var Session
     */
    private $customerSession;

    public function __construct(
        GetCartForUser $getCartForUser,
        GiftCardAccountManagementInterface $giftCardAccountManagement,
        Session $customerSession
    ) {
        $this->getCartForUser = $getCartForUser;
        $this->giftCardAccountManagement = $giftCardAccountManagement;
        $this->customerSession = $customerSession;
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

        if (!$this->customerSession->getCustomerId()) {
            try {
                $this->customerSession->setCustomerId($currentUserId);
            } catch (\Exception $e) {//phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
                //do nothing
            }
        }

        try {
            $this->giftCardAccountManagement->applyGiftCardToCart($cart->getId(), $giftCardCode);
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
