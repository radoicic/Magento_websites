<?php
declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Model\Resolver;

use Amasty\GiftCard\Api\Data\GiftCardOptionInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class GiftCardCartOption implements ResolverInterface
{
    /**
     * @var \Amasty\GiftCard\Model\OptionSource\GiftCardOption
     */
    private $giftCardOption;

    public function __construct(
        \Amasty\GiftCard\Model\OptionSource\GiftCardOption $giftCardOption
    ) {
        $this->giftCardOption = $giftCardOption;
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
        $quoteItem = $value['model'];
        $allDisplayOptions = $this->giftCardOption->getAllDisplayOptions();
        $result = [];

        foreach ($quoteItem->getProduct()->getCustomOptions() as $customOption) {
            if (!isset($allDisplayOptions[$customOption->getCode()])) {
                continue;
            }

            if ($customOption->getCode() == GiftCardOptionInterface::GIFTCARD_AMOUNT) {
                $store = $context->getExtensionAttributes()->getStore();
                $currentCurrency = $store->getCurrentCurrency();
                $baseCurrency = $store->getBaseCurrency();
                $optionValue = $currentCurrency->getCurrencySymbol()
                    . $baseCurrency->convert($customOption->getValue(), $currentCurrency);
            } else {
                $optionValue = $customOption->getValue();
            }

            $result[] = [
                'code' => $customOption->getCode(),
                'label' => $allDisplayOptions[$customOption->getCode()],
                'value' => $optionValue
            ];
        }

        return $result;
    }
}
