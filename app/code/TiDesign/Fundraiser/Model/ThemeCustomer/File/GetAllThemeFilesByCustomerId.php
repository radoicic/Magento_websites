<?php

namespace TiDesign\Fundraiser\Model\ThemeCustomer\File;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class GetAllThemeFilesByCustomerId
{
    /**
     * @param Filesystem $filesystem
     * @param GetDirectoryPathsByCustomerId $getDirectoryPathsByCustomerId
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        protected Filesystem $filesystem,
        protected GetDirectoryPathsByCustomerId $getDirectoryPathsByCustomerId,
        protected StoreManagerInterface $storeManager,
    ) {
    }

    /**
     * @param string $themeId
     * @param int $customerId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute($themeId, $customerId)
    {
        $result = [];
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $directoryPath = $this->getDirectoryPathsByCustomerId->getPublishedDirectoryPath($themeId, $customerId);
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $files = $mediaDirectory->isDirectory($directoryPath) ? $mediaDirectory->read($directoryPath) : [];
        foreach ($files as $fileName) {
            if ($mediaDirectory->isExist($fileName) && $mediaDirectory->isFile($fileName)) {
                $fileName = explode(DIRECTORY_SEPARATOR, $fileName);
                $fileName = array_pop($fileName);
                $result[] = [
                    'file_name' => $fileName,
                    'file_url' => implode(DIRECTORY_SEPARATOR, [$mediaUrl, $directoryPath, $fileName])
                ];
            }
        }
        return $result;
    }
}
