<?php
/**
 * Anowave Magento 2 Tax Switcher
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_TaxSwitch
 * @copyright 	Copyright (c) 2020 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */
 
namespace Anowave\TaxSwitch\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$setup->startSetup();
		
		if (version_compare($context->getVersion(), '2.0.4', '<')) 
		{
			$setup->getConnection()->addColumn($setup->getTable('customer_group'), \Anowave\TaxSwitch\Plugin\Group\Form::FIELD,
			[
				'type'		=> \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
				'length' 	=> 5,
				'unsigned' 	=> true,
				'nullable' 	=> false,
				'default' 	=> '0',
				'comment' 	=> 'Tax display',
			]);
		}
		
		$setup->endSetup();
	}
}