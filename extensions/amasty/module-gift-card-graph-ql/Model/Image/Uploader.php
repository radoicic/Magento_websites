<?php

declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Model\Image;

use Amasty\GiftCard\Model\ConfigProvider;
use Amasty\GiftCard\Utils\FileUpload;
use Amasty\GiftCardGraphQl\Api;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Math\Random;
use Magento\MediaStorage\Model\File\UploaderFactory;

class Uploader implements Api\UploadImageInterface
{
    /**
     * @var Random
     */
    private $random;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var AdapterFactory
     */
    private $adapterFactory;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var UploaderFactory
     */
    private $fileUploaderFactory;

    public function __construct(
        Random $random,
        Filesystem $filesystem,
        AdapterFactory $adapterFactory,
        ConfigProvider $configProvider,
        UploaderFactory $fileUploaderFactory
    ) {
        $this->random = $random;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->adapterFactory = $adapterFactory;
        $this->configProvider = $configProvider;
        $this->fileUploaderFactory = $fileUploaderFactory;
    }

    public function upload(Api\Data\ImageContentInterface $imageContent)
    {
        if (!$this->configProvider->isAllowUserImages()) {
            throw new LocalizedException(__('Custom image uploading is not allowed.'));
        }

        //phpcs:ignore
        if (!($content = base64_decode($imageContent->getBase64EncodedData()))) {
            throw new LocalizedException(__('Image file decoding error.'));
        }

        if (!in_array($imageContent->getExtension(), FileUpload::ALLOWED_EXTENSIONS)) {
            throw new LocalizedException(__('Invalid file extension.'));
        }

        $tmpDirPath = $this->mediaDirectory->getAbsolutePath(FileUpload::AMGIFTCARD_IMAGE_MEDIA_TMP_PATH)
            . DIRECTORY_SEPARATOR;
        $imageAdapter = $this->adapterFactory->create();
        $this->mediaDirectory->create($tmpDirPath);
        $fileName = $this->random->getUniqueHash() . '.' . $imageContent->getExtension();
        $filePath = $tmpDirPath . $fileName;
        $this->mediaDirectory->getDriver()->filePutContents($filePath, $content);

        try {
            $isUploadValid = $imageAdapter->validateUploadFile($filePath);
        } catch (\Exception $e) {
            $isUploadValid = false;
        } finally {
            if (!$isUploadValid) {
                $this->mediaDirectory->delete($filePath);
                throw new LocalizedException(__('Something went wrong during file uploading.'));
            }

            $imageContent->setBase64EncodedData('');
            $imageContent->setFileNameWithExtension($fileName);
        }

        return $imageContent;
    }
}
