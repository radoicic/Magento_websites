<?php

namespace Swissup\Orderattachment\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addFullTextSearchIndex($setup);
        }

        $setup->endSetup();
    }

    protected function addFullTextSearchIndex(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable('swissup_orderattachment');
        $setup->getConnection()->addIndex(
            $table,
            $setup->getConnection()->getIndexName(
                $table,
                ['path', 'comment', 'hash'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['path', 'comment', 'hash'],
            AdapterInterface::INDEX_TYPE_FULLTEXT
        );
    }
}
