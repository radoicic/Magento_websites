<?php

namespace TiDesign\Fundraiser\Model\ThemeCustomer\File;

class CurrentCustomerImagesService
{
    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param GetDirectoryPathsByCustomerId $getDirectoryPathsByCustomerId
     * @param DirectoryProcessor $directoryProcessor
     */
    public function __construct(
        protected \Magento\Customer\Model\Session $customerSession,
        protected GetDirectoryPathsByCustomerId $getDirectoryPathsByCustomerId,
        protected DirectoryProcessor $directoryProcessor
    ) {
    }

    /**
     * @param string $themeId
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function publish($themeId)
    {
        $customerId = $this->customerSession->getCustomerId();
        $draftDirPath = $this->getDirectoryPathsByCustomerId->getDraftDirectoryPath($themeId, $customerId);
        $publishedDirPath = $this->getDirectoryPathsByCustomerId->getPublishedDirectoryPath($themeId, $customerId);

        $this->directoryProcessor->copy($draftDirPath, $publishedDirPath);
    }

    /**
     * @param string $themeId
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function deleteDraft($themeId)
    {
        $customerId = $this->customerSession->getCustomerId();
        $draftDirPath = $this->getDirectoryPathsByCustomerId->getDraftDirectoryPath($themeId, $customerId);
        $this->directoryProcessor->delete($draftDirPath);
    }

    /**
     * @param string $themeId
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function copyToDraft($themeId)
    {
        $customerId = $this->customerSession->getCustomerId();
        $publishedDirPath = $this->getDirectoryPathsByCustomerId->getPublishedDirectoryPath($themeId, $customerId);
        $draftDirPath = $this->getDirectoryPathsByCustomerId->getDraftDirectoryPath($themeId, $customerId);
        $this->directoryProcessor->copy($publishedDirPath, $draftDirPath);
    }
}
