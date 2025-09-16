<?php

namespace TiDesign\Fundraiser\Model\ThemeCustomer\File;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use TiDesign\Fundraiser\Model\File\HTMLSelectorToImageNameEncoder;

class UploadDraftImageProcessor
{
    /**
     * @param UploaderFactory $uploaderFactory
     * @param AdapterFactory $adapterFactory
     * @param Filesystem $filesystem
     * @param GetDirectoryPathsByCustomerId $getDirectoryPathsByCustomerId
     * @param StoreManagerInterface $storeManager
     * @param HTMLSelectorToImageNameEncoder $imageNameEncoder
     */
    public function __construct(
        protected UploaderFactory $uploaderFactory,
        protected AdapterFactory $adapterFactory,
        protected Filesystem $filesystem,
        protected GetDirectoryPathsByCustomerId $getDirectoryPathsByCustomerId,
        protected StoreManagerInterface $storeManager,
        protected HTMLSelectorToImageNameEncoder $imageNameEncoder
    ) {
    }

    /**
     * @param string $fileId
     * @param string $themeId
     * @param int $customerId
     * @param string $htmlSelector
     * @return array|bool
     * @throws \Exception
     */
    public function execute($fileId, $themeId, $customerId, $htmlSelector)
    {
        /** @var \Magento\MediaStorage\Model\File\Uploader $uploaderFactories */
        $uploaderFactories = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploaderFactories->setAllowedExtensions(['jpg', 'jpeg', 'png']);
        $imageAdapter = $this->adapterFactory->create();
        $uploaderFactories->addValidateCallback('custom_image_upload', $imageAdapter, 'validateUploadFile');
        $uploaderFactories->setAllowRenameFiles(false);
        $uploaderFactories->setFilesDispersion(false);
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $directoryPath = $this->getDirectoryPathsByCustomerId->getDraftDirectoryPath($themeId, $customerId);
        $destinationPath = $mediaDirectory->getAbsolutePath($directoryPath);
        $fileName = $this->imageNameEncoder->encode($htmlSelector) . "." . $uploaderFactories->getFileExtension();
        $result = $uploaderFactories->save($destinationPath, $fileName);

        if (!$result) {
            throw new LocalizedException(__('Something went wrong. Please reload the page'));
        }

        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        return implode(DIRECTORY_SEPARATOR, [$mediaUrl, $directoryPath, $result['file']]);
    }
}
