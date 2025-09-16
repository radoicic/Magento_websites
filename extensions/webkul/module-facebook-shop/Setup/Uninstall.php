<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_FacebookShop
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\FacebookShop\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Db\Select;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface as UninstallInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class Uninstall implements UninstallInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $_eavSetupFactory;
    
    private $_mDSetup;
    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        ModuleDataSetupInterface $mDSetup
    ) {
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_mDSetup = $mDSetup;
    }

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $this->_mDSetup]);
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'fb_product_gender');
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'google_product_category');
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'fb_product_brand');
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'product_condition');
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'is_facebook_product');
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'redirect_to_product');
        $installer->endSetup();
    }
}
