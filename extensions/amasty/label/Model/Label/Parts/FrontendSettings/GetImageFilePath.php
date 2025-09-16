<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Product Labels for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Label\Model\Label\Parts\FrontendSettings;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Filesystem\Driver\File;

class GetImageFilePath
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var ImagePathFormatter
     */
    private $imagePathFormatter;

    public function __construct(
        Filesystem $filesystem,
        File $driver,
        ImagePathFormatter $imagePathFormatter
    ) {
        $this->filesystem = $filesystem;
        $this->driver = $driver;
        $this->imagePathFormatter = $imagePathFormatter;
    }

    public function execute(?string $imageName, $checkExists = true): ?string
    {
        $mediaFolderPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $imagePath = $this->imagePathFormatter->execute($imageName);
        $labelImagePath = sprintf('%s%s', $mediaFolderPath, $imagePath);

        if ($checkExists) {
            return $this->driver->isExists($labelImagePath) ? $labelImagePath : null;
        } else {
            return $labelImagePath;
        }
    }
}
