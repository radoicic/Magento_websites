<?php 

namespace TiDesign\Ekleme\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $customerSetupFactory;

    public function __construct(
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);


        $customerSetup->addAttribute(
            Customer::ENTITY,
            'consignment_active',
            [
                'type' => 'int',
                'label' => __('Consignment Customer'),
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => false,
                'default' => 0,
                'visible' => true,
                'admin_only' => true,
                'system' => 0,
				'user_defined' => false,
				'is_user_defined' => false,
				'sort_order' => 1000,
				'is_used_in_grid' => false,
				'is_visible_in_grid' => false,
				'is_filterable_in_grid' => false,
				'is_searchable_in_grid' => false,
            ]
        );

        $customerSetup
            ->getEavConfig()
            ->getAttribute(
                'customer',
                'consignment_active'
            )
            ->setData(
                'used_in_forms',
                ['adminhtml_customer']
            )
            ->save();
			
        $customerSetup->addAttribute(
            Customer::ENTITY,
            'consignment_store',
            [
                'type' => 'int',
                'label' => __('Consignment Store'),
                'input' => 'select',
                'source' => 'TiDesign\Domainmanager\Model\Config\Attribute\Storelist',
                'required' => false,
                'default' => 0,
                'visible' => true,
                'admin_only' => true,
                'system' => 0,
				'user_defined' => false,
				'is_user_defined' => false,
				'sort_order' => 1010,
				'is_used_in_grid' => false,
				'is_visible_in_grid' => false,
				'is_filterable_in_grid' => false,
				'is_searchable_in_grid' => false,
            ]
        );

        $customerSetup
            ->getEavConfig()
            ->getAttribute(
                'customer',
                'consignment_store'
            )
            ->setData(
                'used_in_forms',
                ['adminhtml_customer']
            )
            ->save();

        $setup->endSetup();
    }
}