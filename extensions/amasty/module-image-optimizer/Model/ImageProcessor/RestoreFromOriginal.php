<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\ImageProcessor;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;
use Amasty\ImageOptimizer\Api\Data\QueueInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class RestoreFromOriginal implements ImageProcessorInterface
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    public function __construct(Filesystem $filesystem)
    {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    public function process(QueueInterface $queue): void
    {
        if ($this->mediaDirectory->isExist(DumpOriginal::DUMP_DIRECTORY . $queue->getFilename())) {
            $this->mediaDirectory->copyFile(
                DumpOriginal::DUMP_DIRECTORY . $queue->getFilename(),
                $queue->getFilename()
            );
        }
    }

    public function prepareQueue(string $file, ImageSettingInterface $imageSetting, QueueInterface $queue): bool
    {
        return false;
    }
}
