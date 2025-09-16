<?php
declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Model\Resolver;

use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardAccount\Model\GiftCardAccount\GiftCardCartProcessor;
use Amasty\GiftCardAccount\Model\GiftCardAccount\ResourceModel\CollectionFactory;
use Amasty\GiftCardGraphQl\Utils\MoneyFormatter;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Api\Data\CartInterface;

class GetAppliedGiftCardsFromCart implements ResolverInterface
{
    /**
     * @var MoneyFormatter
     */
    private $formatter;

    /**
     * @var CollectionFactory
     */
    private $accountCollectionFactory;

    public function __construct(
        CollectionFactory $accountCollectionFactory,
        MoneyFormatter $formatter
    ) {
        $this->formatter = $formatter;
        $this->accountCollectionFactory = $accountCollectionFactory;
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
            throw new GraphQlInputException(__('"cart" value should be specified'));
        }
        /** @var CartInterface $cart */
        $cart = $value['model'];
        $cartGiftCards = $this->getCartGiftCards($cart);

        if (empty($cartGiftCards)) {
            return [];
        }
        $appliedGiftCards = [];
        $accounts = $this->getAccountsByCards($cartGiftCards);
        $store = $context->getExtensionAttributes()->getStore();

        /** @var GiftCardAccountInterface $account */
        foreach ($accounts as $account) {
            $appliedGiftCards[]= [
                'code' => $cartGiftCards[$account->getAccountId()][GiftCardCartProcessor::GIFT_CARD_CODE],
                'current_balance' => $this->formatter->format($account->getCurrentValue(), $store),
                'applied_balance' => [
                    'value' => $cartGiftCards[$account->getAccountId()][GiftCardCartProcessor::GIFT_CARD_AMOUNT],
                    'currency' => $store->getCurrentCurrency()->getCode()
                ],
                'expiration_date' => $account->getExpiredDate(),
            ];
        }

        return $appliedGiftCards;
    }

    /**
     * @param CartInterface $cart
     *
     * @return array
     */
    private function getCartGiftCards(CartInterface $cart): array
    {
        $appliedCards = [];

        if (!$cart->getExtensionAttributes()) {
            return $appliedCards;
        }

        if ($cart->getExtensionAttributes()->getAmAppliedGiftCards()) {
            $cartGiftCards = $cart->getExtensionAttributes()->getAmAppliedGiftCards();
        } elseif ($cart->getExtensionAttributes()->getAmGiftcardQuote()) {
            $cartGiftCards = $cart->getExtensionAttributes()->getAmGiftcardQuote()->getGiftCards();
        } else {
            $cartGiftCards = [];
        }

        foreach ($cartGiftCards as $giftCard) {
            $appliedCards[$giftCard[GiftCardCartProcessor::GIFT_CARD_ID]] = $giftCard;
        }

        return $appliedCards;
    }

    /**
     * @param array $cards
     *
     * @return GiftCardAccountInterface[]
     */
    private function getAccountsByCards(array $cards): array
    {
        $codesList = [];

        foreach ($cards as $card) {
            $codesList[] = $card[GiftCardCartProcessor::GIFT_CARD_ID];
        }

        return $this->accountCollectionFactory->create()->addFieldToFilter(
            GiftCardAccountInterface::ACCOUNT_ID,
            ['in' => $codesList]
        )->getItems();
    }
}
