<?php

namespace TiDesign\Fundraiser\Model\ThemeCustomer\File;

use TiDesign\Fundraiser\Model\Theme\ThemeIDToFolderNameFormatter;

class GetDirectoryPathsByCustomerId
{
    const UPLOAD_DIRECTORY_PATH = 'TiDesign/Fundraiser/Upload';

    /**
     * @param ThemeIDToFolderNameFormatter $themeIDToFolderNameFormatter
     */
    public function __construct(
        protected ThemeIDToFolderNameFormatter $themeIDToFolderNameFormatter
    ) {
    }

    /**
     * @param $themeId
     * @param $customerId
     * @return string
     */
    public function getDraftDirectoryPath($themeId, $customerId)
    {
        $folderName = $this->themeIDToFolderNameFormatter->format($themeId);
        $paths = [self::UPLOAD_DIRECTORY_PATH, $folderName, $customerId, 'DRAFT'];
        return implode(DIRECTORY_SEPARATOR, $paths);
    }


    /**
     * @param $themeId
     * @param $customerId
     * @return string
     */
    public function getPublishedDirectoryPath($themeId, $customerId)
    {
        $folderName = $this->themeIDToFolderNameFormatter->format($themeId);
        $paths = [self::UPLOAD_DIRECTORY_PATH, $folderName, $customerId, 'PUBLISHED'];
        return implode(DIRECTORY_SEPARATOR, $paths);
    }
}
