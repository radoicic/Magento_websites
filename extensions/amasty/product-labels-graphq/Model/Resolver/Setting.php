<?php

declare(strict_types=1);

namespace Amasty\LabelGraphQl\Model\Resolver;

use Amasty\Label\Model\ConfigProvider;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;

class Setting implements ResolverInterface
{
    /**
     * @var ConfigProvider
     */
    private $settings;

    public function __construct(
        ConfigProvider $settings
    ) {
        $this->settings = $settings;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|Value|mixed
     * @throws \Exception
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        try {
            return [
                'product_container' => $this->settings->getProductContainerPath(),
                'category_container' => $this->settings->getProductListContainerPath(),
                'max_labels' => $this->settings->getMaxLabels(),
                'show_several_on_place' => (int) $this->settings->isShowSeveralOnPlace(),
                'labels_alignment' => $this->settings->getLabelAlignment(),
                'margin_between' => $this->settings->getMarginBetween()
            ];
        } catch (\Exception $e) {
            throw new GraphQlNoSuchEntityException(__('Something went wrong during getting settings data'));
        }
    }
}
