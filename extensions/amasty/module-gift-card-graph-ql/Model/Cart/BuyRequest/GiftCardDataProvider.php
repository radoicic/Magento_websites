<?php
declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Model\Cart\BuyRequest;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\QuoteGraphQl\Model\Cart\BuyRequest\BuyRequestDataProviderInterface;

class GiftCardDataProvider implements BuyRequestDataProviderInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $cartItemData): array
    {
        return $this->arrayManager->get('gift_card_options', $cartItemData, []);
    }
}
