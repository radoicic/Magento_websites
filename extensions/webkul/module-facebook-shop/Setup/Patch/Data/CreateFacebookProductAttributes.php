<?php
/*
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_FacebookShop
 * @author    Webkul
 * @copyright Copyright(c)Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\FacebookShop\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Catalog\Model\Config as Config;
use Webkul\FacebookShop\Helper\Data as ShopHelper;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Patch is mechanism, that allows to do atomic upgrade data changes
 */
class CreateFacebookProductAttributes implements
    DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        EavConfig $eavConfig,
        Config $config
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->config = $config;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'is_facebook_product',
            [
                'type' => 'int',
                'group' => 'Facebook Shop',
                'backend' => '',
                'frontend' => '',
                'label' => __('Allow On Facebook Shop'),
                'input' => 'boolean',
                'class' => '',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'default' => 0,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple,virtual,configurable,downloadable,grouped,bundle'
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'redirect_to_product',
            [
                'type' => 'int',
                'group' => 'Facebook Shop',
                'backend' => '',
                'frontend' => '',
                'label' => __('Redirect to Product Page'),
                'input' => 'boolean',
                'class' => '',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'default' => 0,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple,virtual,configurable,downloadable'
            ]
        );
        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'product_condition',
            [
                'type' => 'varchar',
                'group' => 'Facebook Shop',
                'backend' => '',
                'frontend' => '',
                'label' => __('Product Condition'),
                'input' => 'select',
                'class' => '',
                'source' => \Webkul\FacebookShop\Model\Source\ConditionTypes::class,
                'default' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple,virtual,configurable,downloadable,grouped,bundle'
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'fb_product_gender',
            [
                'type' => 'varchar',
                'group' => 'Facebook Shop',
                'backend' => '',
                'frontend' => '',
                'label' => __('Gender'),
                'input' => 'select',
                'class' => '',
                'source' => \Webkul\FacebookShop\Model\Source\Gender::class,
                'default' => "",
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple,virtual,configurable,downloadable,grouped,bundle'
            ]
        );
         /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
         $eavSetup->addAttribute(
             \Magento\Catalog\Model\Product::ENTITY,
             'google_product_category',
             [
                'type' => 'varchar',
                'group' => 'Facebook Shop',
                'backend' => \Webkul\FacebookShop\Model\Attribute\Backend\ValidateFbClass::class,
                'frontend' => '',
                'label' => __('Google Product Category'),
                'input' => 'text',
                'class' => '',
                'frontend_class' => '',
                'source' => '',
                'default' => "",
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple,virtual,configurable,downloadable,grouped,bundle',
                'note' => 'Please visit '.ShopHelper::GOOGLEPRODUTTAXONOMYTXTFILE.'
                  to find related id for your product '
             ]
         );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'fb_product_brand',
            [
                'type' => 'varchar',
                'group' => 'Facebook Shop',
                'backend' => \Webkul\FacebookShop\Model\Attribute\Backend\ValidateFbClass::class,
                'frontend' => '',
                'label' => __('Brand'),
                'input' => 'text',
                'class' => '',
                'frontend_class' => '',
                'source' => '',
                'default' => "",
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple,virtual,configurable,downloadable,grouped,bundle',
                'note' => 'Example: Facebook'
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [

        ];
    }
}
