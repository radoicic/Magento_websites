<?php

declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Model\GiftCard;

use Amasty\GiftCard\Api\Data\GiftCardEmailInterfaceFactory;
use Amasty\GiftCard\Api\Data\GiftCardOptionInterface;
use Amasty\GiftCard\Model\ConfigProvider;
use Amasty\GiftCard\Model\Image\Repository;
use Amasty\GiftCard\Utils\FileUpload;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class EmailPreviewProcessor extends \Amasty\GiftCard\Model\GiftCard\EmailPreviewProcessor
{
    /**
     * @var FileUpload
     */
    private $fileUpload;

    public function __construct(
        Repository $imageRepository,
        FileUpload $fileUpload,
        FactoryInterface $templateFactory,
        ConfigProvider $config,
        GiftCardEmailInterfaceFactory $cardEmailFactory,
        CurrencyInterface $localeCurrency,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct(
            $imageRepository,
            $fileUpload,
            $templateFactory,
            $config,
            $cardEmailFactory,
            $localeCurrency,
            $storeManager
        );
        $this->fileUpload = $fileUpload;
    }

    protected function getPreviewImage(array $requestData): string
    {
        if ($customImage = $requestData[GiftCardOptionInterface::CUSTOM_IMAGE] ?? null) {
            return '<img src="' . $this->fileUpload->getTempImgUrl($customImage) . '"/>';
        }

        return parent::getPreviewImage($requestData);
    }
}
