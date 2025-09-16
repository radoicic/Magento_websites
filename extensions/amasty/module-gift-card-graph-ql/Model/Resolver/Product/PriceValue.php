<?php
declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Model\Resolver\Product;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class PriceValue implements ResolverInterface
{
    /**
     * @var \Amasty\GiftCardGraphQl\Utils\MoneyFormatter
     */
    private $formatter;

    public function __construct(
        \Amasty\GiftCardGraphQl\Utils\MoneyFormatter $formatter
    ) {
        $this->formatter = $formatter;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $price = $value[$field->getName()] ?? '';

        return array_merge($this->formatter->format(
            $price,
            $context->getExtensionAttributes()->getStore()
        ), ['default' => $price]);
    }
}
