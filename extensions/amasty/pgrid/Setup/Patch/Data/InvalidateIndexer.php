<?php
declare(strict_types=1);

namespace Amasty\Pgrid\Setup\Patch\Data;

use Amasty\Pgrid\Model\Indexer\QtySoldProcessor;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class InvalidateIndexer implements DataPatchInterface
{
    /**
     * @var QtySoldProcessor
     */
    private $processor;

    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    public function __construct(
        QtySoldProcessor $processor,
        ResourceInterface $moduleResource
    ) {
        $this->processor = $processor;
        $this->moduleResource = $moduleResource;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): void
    {
        $setupDataVersion = $this->moduleResource->getDataVersion('Amasty_Pgrid');
        if ($setupDataVersion && version_compare($setupDataVersion, '1.5.0', '<')) {
            $this->processor->markIndexerAsInvalid();
        }
    }
}
