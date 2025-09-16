<?php
/**
 * Created by PhpStorm.
 * User: Meetanshi 23
 * Date: 13-04-2022
 * Time: 14:44
 */

namespace Meetanshi\Callforprice\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class CallforpriceAttribute implements DataPatchInterface
{
    /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;

    /** @var EavSetupFactory */
    private $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttributeGroup(
            'catalog_product',
            min($eavSetup->getAllAttributeSetIds('catalog_product')),
            'Call For Price',
            400
        );
        $eavSetup->addAttribute('catalog_product', 'enable_callforprice', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Call For Price',
            'input' => 'boolean',
            'class' => '',
            'global' => Attribute::SCOPE_GLOBAL,
            'group' => 'Call For Price',
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => 0,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => false,
            'unique' => false,
            'apply_to' => ''
        ]);

        $eavSetup->addAttribute('catalog_product', 'callforprice_text', [
            'type' => 'text',
            'backend' => '',
            'frontend' => '',
            'label' => 'Button Text',
            'global' => Attribute::SCOPE_GLOBAL,
            'group' => 'Call For Price',
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => false,
            'unique' => false,
            'apply_to' => ''
        ]);

        $eavSetup->addAttribute('catalog_product', 'callforprice_label_text', [
            'type' => 'text',
            'backend' => '',
            'frontend' => '',
            'label' => 'Label Text',
            'global' => Attribute::SCOPE_GLOBAL,
            'group' => 'Call For Price',
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => false,
            'unique' => false,
            'apply_to' => ''
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
