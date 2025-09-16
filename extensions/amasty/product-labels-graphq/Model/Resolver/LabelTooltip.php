<?php

declare(strict_types=1);

namespace Amasty\LabelGraphQl\Model\Resolver;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Api\Data\LabelTooltipInterface;
use Amasty\Label\ViewModel\Label\TextProcessor;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class LabelTooltip implements ResolverInterface
{
    /**
     * @var TextProcessor
     */
    private $textProcessor;

    public function __construct(
        TextProcessor $textProcessor
    ) {
        $this->textProcessor = $textProcessor;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null): array
    {
        $model = $value[LabelProvider::MODEL_KEY] ?? null;

        if ($model instanceof LabelInterface) {
            $tooltip = $model->getExtensionAttributes()->getLabelTooltip();

            return [
                LabelTooltipInterface::STATUS => $tooltip->getStatus(),
                LabelTooltipInterface::COLOR => $tooltip->getColor(),
                LabelTooltipInterface::TEXT_COLOR => $tooltip->getTextColor(),
                LabelTooltipInterface::TEXT => $this->textProcessor->renderLabelText($tooltip->getText(), $model)
            ];
        } else {
            throw new GraphQlInputException(__('"model" value should be specified'));
        }
    }
}
