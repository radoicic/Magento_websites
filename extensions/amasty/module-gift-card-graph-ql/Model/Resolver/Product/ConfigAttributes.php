<?php
declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Model\Resolver\Product;

use Amasty\GiftCard\Model\ConfigProvider;
use Amasty\GiftCard\Model\GiftCard\Product\Type\GiftCard;
use Amasty\GiftCard\Model\GiftCard\Attributes;
use Magento\Catalog\Model\Product;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ConfigAttributes implements ResolverInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     *
     * @return string|null
     * @throws GraphQlInputException
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
        $data = null;

        if ($product->getTypeId() === GiftCard::TYPE_AMGIFTCARD) {
            if ($product->getData($field->getName()) == Attributes::ATTRIBUTE_CONFIG_VALUE) {
                switch ($field->getName()) {
                    case Attributes::GIFTCARD_LIFETIME:
                        $data = $this->configProvider->getLifetime();
                        break;
                    case Attributes::EMAIL_TEMPLATE:
                        $data = $this->configProvider->getEmailTemplate();
                        break;
                }
            } else {
                $data = $product->getData($field->getName());
            }
        }

        return $data;
    }
}
