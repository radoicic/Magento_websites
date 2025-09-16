<?php

namespace TiDesign\Fundraiser\Model\ThemeCustomer\File;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class DirectoryProcessor
{
    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param GetDirectoryPathsByCustomerId $getDirectoryPathsByCustomerId
     * @param Filesystem $filesystem
     */
    public function __construct(
        protected \Magento\Customer\Model\Session $customerSession,
        protected Filesystem $filesystem,
        protected GetDirectoryPathsByCustomerId $getDirectoryPathsByCustomerId,
    ) {
    }

    /**
     * @param string $sourceDirPath
     * @param string $destinationDirPath
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function copy($sourceDirPath, $destinationDirPath)
    {
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $sourceAbsolutePath = $mediaDirectory->getAbsolutePath($sourceDirPath);
        $destinationAbsolutePath = $mediaDirectory->getAbsolutePath($destinationDirPath);

        $mediaDirectory->delete($destinationDirPath);
        $mediaDirectory->create($destinationDirPath);

        $files = $mediaDirectory->isDirectory($sourceDirPath) ? $mediaDirectory->read($sourceDirPath) : [];
        foreach ($files as $fileName) {
            if ($mediaDirectory->isExist($fileName) && $mediaDirectory->isFile($fileName)) {
                $fileName = explode(DIRECTORY_SEPARATOR, $fileName);
                $fileName = array_pop($fileName);
                $source = implode(DIRECTORY_SEPARATOR, [$sourceAbsolutePath, $fileName]);
                $destination = implode(DIRECTORY_SEPARATOR, [$destinationAbsolutePath, $fileName]);
                $mediaDirectory->copyFile($source, $destination);
            }
        }
    }

    /**
     * @param string $dirPath
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function delete($dirPath)
    {
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $mediaDirectory->delete($dirPath);
    }
}
