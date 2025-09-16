<?php
declare(strict_types=1);

namespace Amasty\Pgrid\Setup;

use Amasty\Pgrid\Api\Data\QtySoldInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    public const TABLE_NAMES = [
        QtySoldInterface::QTY_SOLD_TABLE
    ];

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this
            ->uninstallTables($setup)
            ->uninstallConfigData($setup);
    }

    private function uninstallTables(SchemaSetupInterface $setup): self
    {
        $setup->startSetup();
        foreach (self::TABLE_NAMES as $tableName) {
            $setup->getConnection()->dropTable($setup->getTable($tableName));
        }
        $setup->endSetup();

        return $this;
    }

    private function uninstallConfigData(SchemaSetupInterface $setup): self
    {
        $configTable = $setup->getTable('core_config_data');
        $setup->getConnection()->delete($configTable, "`path` LIKE 'amasty_pgrid/%'");

        return $this;
    }
}
