<?php
namespace TiDesign\BundleProduct\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class CustomProductAttributes implements DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory;
     */
    private $eavSetupFactory;
    
    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    private $attributeSetFactory;

    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavConfig $eavConfig
     * @param EavSetupFactory $eavSetupFactory
     * @param SetFactory $attributeSetFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavConfig $eavConfig,
        EavSetupFactory $eavSetupFactory,
        SetFactory $attributeSetFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavConfig = $eavConfig;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
		$eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        /** creating attribute for bundle products */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tidesign_custom_bundle',
            [
				'group' 					=> 'Product Details',
                'type' 						=> 'int',
                'backend' 					=> '',
                'frontend' 					=> '',
                'label' 					=> 'Custom Bundle Display',
                'input' 					=> 'boolean',
                'class' 					=> '',
                'source' 					=> 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'global' 					=> \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' 					=> true,
                'required' 					=> false,
                'user_defined' 				=> false,
                'default' 					=> '',
                'searchable' 				=> false,
                'filterable' 				=> false,
                'comparable' 				=> false,
                'visible_on_front' 			=> false,
                'used_in_product_listing' 	=> true,
                'unique' 					=> false,
                'apply_to' 					=> 'bundle'
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }
}