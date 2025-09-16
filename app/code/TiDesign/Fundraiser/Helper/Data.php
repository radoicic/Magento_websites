<?php

namespace TiDesign\Fundraiser\Helper;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;

class Data extends AbstractHelper
{
    public function __construct(
        Context $context,
        protected ModuleDirReader $moduleDirReader,
        protected Session $customerSession,
        protected DirectoryList $directoryList,
        protected FileDriver $fileDriver
    ) {
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->customerSession->isLoggedIn() ? true : false;
    }

    /**
     * @return string[]
     */
    public function getAllThemes(): array
    {
        $themes = [];
        $modulePath = $this->moduleDirReader->getModuleDir('', 'TiDesign_Fundraiser');
        $dirPath = "$modulePath/view/frontend/templates/themes";
        if ($this->isExistPath($dirPath) && $this->isDirectory($dirPath)) {
            $themePaths = $this->fileDriver->readDirectory($dirPath);
            foreach ($themePaths as $path) {
                $name = basename($path);
                if ($this->isDirectory($path) && $this->isExistPath("$dirPath/$name/theme.phtml")) {
                    $themes[] = $name;
                }
            }
        }
        return $themes;
    }

    /**
     * @param string $path
     * @return bool
     */
    private function isExistPath($path)
    {
        return $this->fileDriver->isExists($path);
    }

    /**
     * @param string $path
     * @return bool
     */
    private function isDirectory($path)
    {
        return $this->fileDriver->isDirectory($path);
    }
}
