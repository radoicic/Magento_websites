<?php

declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Model\Resolver;

use Amasty\GiftCard\Api\Data\GiftCardOptionInterface;
use Amasty\GiftCard\Model\GiftCard\EmailPreviewProcessor;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Psr\Log\LoggerInterface;

class PreviewGiftCard implements ResolverInterface
{
    /**
     * @var EmailPreviewProcessor
     */
    private $previewProcessor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        EmailPreviewProcessor $previewProcessor,
        LoggerInterface $logger
    ) {
        $this->previewProcessor = $previewProcessor;
        $this->logger = $logger;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        try {
            $baseUrl = $context->getExtensionAttributes()->getStore()->getBaseUrl();
            $content = $this->previewProcessor->process($args['input'] ?? []);

            $content = str_replace($baseUrl . 'media', '/media', $content);
        } catch (\Exception $e) {
            $content = '';
            $this->logger->critical($e);
        }

        return [
            'content' => $content
        ];
    }
}
