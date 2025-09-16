<?php

namespace TiDesign\Fundraiser\Model\ThemeCustomer;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Serialize\Serializer\Json;
use TiDesign\Fundraiser\Model\File\HTMLSelectorToImageNameEncoder;
use TiDesign\Fundraiser\Model\Theme\GetByThemeId;
use TiDesign\Fundraiser\Model\Theme\OptionSource\EditorTypes;
use TiDesign\Fundraiser\Model\Theme\ThemeIDToFolderNameFormatter;
use TiDesign\Fundraiser\Model\ThemeCustomer\File\GetAllThemeFilesByCustomerId;
use TiDesign\Fundraiser\Model\ThemeCustomer\File\UploadDraftImageProcessor;

class GetThemeCustomerConfigDataByCurrentCustomerId
{
    /**
     * @param GetThemeByCurrentCustomerId $getThemeByCurrentCustomerId
     * @param GetByThemeId $getByThemeId
     * @param Json $serializer
     * @param ThemeIDToFolderNameFormatter $themeIDToFolderNameFormatter
     * @param HTMLSelectorToImageNameEncoder $imageNameEncoder
     * @param GetAllThemeFilesByCustomerId $getAllThemeFilesByCustomerId
     */
    public function __construct(
        protected GetThemeByCurrentCustomerId $getThemeByCurrentCustomerId,
        protected GetByThemeId $getByThemeId,
        protected Json $serializer,
        protected ThemeIDToFolderNameFormatter $themeIDToFolderNameFormatter,
        protected HTMLSelectorToImageNameEncoder $imageNameEncoder,
        protected GetAllThemeFilesByCustomerId $getAllThemeFilesByCustomerId
    ) {
    }

    /**
     * @param $themeId
     * @return string
     */
    public function execute($themeId)
    {
        $themeCustomer = $this->getThemeByCurrentCustomerId->execute($themeId);
        if (!($themeCustomer->getConfigData())) {
            return null;
        }
        $theme = $this->getByThemeId->execute($themeId);
        if (!($theme->getConfigData())) {
            return null;
        }
        return $this->bindThemeConfigDataIntoThemeCustomerConfigData($themeCustomer, $theme);
    }

    /**
     * @param \TiDesign\Fundraiser\Model\ThemeCustomer $themeCustomer
     * @param \TiDesign\Fundraiser\Model\Theme $theme
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function bindThemeConfigDataIntoThemeCustomerConfigData($themeCustomer, $theme)
    {
        $themeCustomerConfigData = $this->serializer->unserialize($themeCustomer->getConfigData());
        $themeConfigData = $this->serializer->unserialize($theme->getConfigData());
        $files = $this->getAllThemeFilesByCustomerId->execute($theme->getThemeId(), $themeCustomer->getCustomerId());
        $themeEditors = $themeConfigData['editors'];
        foreach ($themeEditors as $key => $config) {
            $type = $config['type'] ?? null;
            $selector = $config['target_selector'] ?? null;
            if ($type !== EditorTypes::TYPE_IMAGE_UPLOADER || !$selector) {
                continue;
            }
            $fileName = $this->imageNameEncoder->encode($selector);
            foreach ($files as $file) {
                if (str_starts_with($file['file_name'], $fileName)) {
                    $themeCustomerConfigData[$key] = $file['file_url'];
                }
            }
        }
        return $this->serializer->serialize($themeCustomerConfigData);
    }
}
