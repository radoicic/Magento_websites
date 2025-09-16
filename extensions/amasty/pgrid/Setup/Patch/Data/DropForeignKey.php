<?php
declare(strict_types=1);

namespace Amasty\Pgrid\Setup\Patch\Data;

use Amasty\Pgrid\Api\Data\QtySoldInterface;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\NonTransactionableInterface;

class DropForeignKey implements DataPatchInterface, NonTransactionableInterface
{
    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    public function __construct(
        ModuleDataSetupInterface $setup,
        ResourceInterface $moduleResource
    ) {
        $this->setup = $setup;
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
        if ($setupDataVersion && version_compare($setupDataVersion, '1.5.1', '<')) {
            $this->dropForeignKey($this->setup);
        }
    }

    private function dropForeignKey($setup): void
    {
        $connection = $setup->getConnection();
        $table = $setup->getTable(QtySoldInterface::QTY_SOLD_TABLE);
        $productTable = $setup->getTable('catalog_product_entity');
        $keyName = $connection->getForeignKeyName(
            $table,
            QtySoldInterface::PRODUCT_ID,
            $productTable,
            QtySoldInterface::PRODUCT_ID
        );

        $connection->dropForeignKey($table, $keyName);
    }
}
