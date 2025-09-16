<?php
declare(strict_types=1);

namespace Amasty\Pgrid\Setup\Patch\Data;

use Magento\Catalog\Api\Data\EavAttributeInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateSpecialPriceAttribute implements DataPatchInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        ResourceInterface $moduleResource,
        ModuleDataSetupInterface $setup
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->moduleResource = $moduleResource;
        $this->setup = $setup;
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
        if (!$setupDataVersion || version_compare($setupDataVersion, '1.5.2', '<')) {
            $this->upgrade($this->setup);
        }
    }

    private function upgrade(ModuleDataSetupInterface $setup): void
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->updateAttribute(
            ProductAttributeInterface::ENTITY_TYPE_CODE,
            'special_from_date',
            EavAttributeInterface::IS_FILTERABLE_IN_GRID,
            true
        );
        $eavSetup->updateAttribute(
            ProductAttributeInterface::ENTITY_TYPE_CODE,
            'special_to_date',
            EavAttributeInterface::IS_FILTERABLE_IN_GRID,
            true
        );
    }
}
