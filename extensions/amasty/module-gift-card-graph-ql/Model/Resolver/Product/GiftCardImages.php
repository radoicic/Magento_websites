<?php

declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Model\Resolver\Product;

use Amasty\GiftCard\Api\ImageRepositoryInterface;
use Amasty\GiftCard\Utils\FileUpload;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Query\Uid;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class GiftCardImages implements ResolverInterface
{
    /**
     * @var FileUpload
     */
    private $fileUpload;

    /**
     * @var ImageRepositoryInterface
     */
    private $imageRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Uid|null
     */
    private $uidEncoder;

    public function __construct(
        FileUpload $fileUpload,
        ImageRepositoryInterface $imageRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->fileUpload = $fileUpload;
        $this->imageRepository = $imageRepository;
        $this->productRepository = $productRepository;
        $this->uidEncoder = class_exists(Uid::class)
            ? ObjectManager::getInstance()->create(Uid::class)
            : null;
    }

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
        $product = $this->productRepository->getById($product->getId());
        $baseUrl = $context->getExtensionAttributes()->getStore()->getBaseUrl();

        return array_map(function ($imageId) use ($baseUrl) {
            $image = $this->imageRepository->getById((int)$imageId);
            $imageUrl = $this->fileUpload->getImageUrl($image->getImagePath(), $image->isUserUpload());
            $image->setImagePath(str_replace($baseUrl, '', $imageUrl));

            $imageId = (string)$image->getImageId();
            if ($this->uidEncoder !== null) {
                $uid = $this->uidEncoder->encode($imageId);
            } else {
                //phpcs:ignore Magento2.Functions.DiscouragedFunction
                $uid = base64_encode($imageId);
            }
            $image->setData('uid', $uid);

            return $image;
        }, explode(',', (string)$product->getAmGiftcardCodeImage()));
    }
}
