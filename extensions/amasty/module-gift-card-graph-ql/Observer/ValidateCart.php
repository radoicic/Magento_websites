<?php
declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Observer;

use Amasty\GiftCardAccount\Model\CartValidator;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ValidateCart implements ObserverInterface
{
    /**
     * @var CartValidator
     */
    private $cartValidator;

    public function __construct(
        CartValidator $cartValidator
    ) {
        $this->cartValidator = $cartValidator;
    }

    public function execute(Observer $observer)
    {
        if ($cart = $observer->getEvent()->getQuote()) {
            $this->cartValidator->validate($cart);
        }
    }
}
