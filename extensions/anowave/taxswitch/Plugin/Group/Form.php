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
 
namespace Anowave\TaxSwitch\Plugin\Group;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\GroupFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Registry;

class Form
{
	const FIELD = 'group_tax';
	
	/**
	 * No default group display
	 *
	 * @var integer
	 */
	const DISPLAY_TYPE_NONE = 0;
	
	/**
	 * @var FilterBuilder
	 */
	protected $_filterBuilder;
	
	/**
	 * @var GroupRepositoryInterface
	 */
	protected $_groupRepository;
	
	/**
	 * @var SearchCriteriaBuilder
	 */
	protected $_searchCriteriaBuilder;
	
	/**
	 * @var GroupFactory
	 */
	protected $_groupFactory;
	
	/**
	 * @var Registry
	 */
	protected $_coreRegistry;
	
	/**
	 * Constructor 
	 * 
	 * @param FilterBuilder $filterBuilder
	 * @param GroupRepositoryInterface $groupRepository
	 * @param SearchCriteriaBuilder $searchCriteriaBuilder
	 * @param GroupFactory $groupFactory
	 */
	public function __construct
	(
		FilterBuilder $filterBuilder,
		GroupRepositoryInterface $groupRepository, 
		SearchCriteriaBuilder $searchCriteriaBuilder, 
		GroupFactory $groupFactory,
		Registry $registry
	)
	{
		$this->_filterBuilder = $filterBuilder;
		
		/**
		 * Set group respository 
		 * 
		 * @var GroupRepositoryInterface $_groupRepository
		 */
		$this->_groupRepository = $groupRepository;
		
		/**
		 * Set search criteria builder
		 * 
		 * @var SearchCriteriaBuilder $_searchCriteriaBuilder
		 */
		$this->_searchCriteriaBuilder = $searchCriteriaBuilder;
		
		/**
		 * Set group factory 
		 * 
		 * @var GroupFactory $_groupFactory
		 */
		$this->_groupFactory = $groupFactory;
		
		/**
		 * Set registry 
		 * 
		 * @var Registry $_coreRegistry
		 */
		$this->_coreRegistry = $registry;
	}    
	
	public function afterSetForm
	(
		\Magento\Customer\Block\Adminhtml\Group\Edit\Form $context
	)
	{
		$form = $context->getForm();
		
		$group_id = (int) $this->_coreRegistry->registry(RegistryConstants::CURRENT_GROUP_ID);
		
		if (!is_null($group_id))
		{
			$group = $this->_groupFactory->create();
			
			/**
			 * Load group by id
			 */
			$group->load($group_id);
			
			/**
			 * Get tax display 
			 * 
			 * @var int $value
			 */
			$value = (int) $group->getData(static::FIELD);
		}
		else 
		{
			$value = 0;
		}

		$fieldset = $form->addFieldset('base1_fieldset', ['legend' => __('Tax display settings')]);
		
		$group_tax = $fieldset->addField(static::FIELD, 'select',
		[
			'name' 		=> static::FIELD,
			'label' 	=> __('Tax display'),
			'title' 	=> __('Tax display'),
			'required' 	=> false,
			'values' 	=> array
			(
				static::DISPLAY_TYPE_NONE								=> __('- Please select -'),
				\Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX 	=> __('Incl. tax'),
				\Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX 	=> __('Excl. tax'),
				\Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH 			=> __('Both')
			),
			'value' => $value
		]);
		
		return $form;
	} 
	
	public function afterExecute(\Magento\Customer\Controller\Adminhtml\Group\Save $save, $result)
	{
		/**
		 * Get group display 
		 * 
		 * @var int $group_tax
		 */
		$group_tax = (int) $save->getRequest()->getParam(static::FIELD);
		
		if (!in_array($group_tax, 
		[
			\Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX,
			\Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX,
			\Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH
		]))
		{
			$group_tax = 0;
		}
		
		/**
		 * Get code
		 * 
		 * @var string $code
		 */
		$code = $save->getRequest()->getParam('code');
		
		if(empty($code)) 
		{
			$code = 'NOT LOGGED IN';
		}
		
		/**
		 * Create search filter 
		 * 
		 * @var array $_filter
		 */
		$_filter = 
		[ 
			$this->_filterBuilder->setField('customer_group_code')->setConditionType('eq')->setValue($code)->create()
		];
		
		/**
		 * Load all groups by code 
		 * 
		 * @var array $customerGroups
		 */
		$customerGroups = $this->_groupRepository->getList($this->_searchCriteriaBuilder->addFilters($_filter)->create())->getItems();
		
		/**
		 * Get first group 
		 * 
		 * @var Ambiguous $customerGroup
		 */
		$customerGroup = array_shift($customerGroups);
		
		if($customerGroup)
		{
			$group = $this->_groupFactory->create();

			/**
			 * Load group by id
			 */
			$group->load($customerGroup->getId());

			/**
			 * Set group tax display
			 */
			$group->setData(static::FIELD,$group_tax);
			
			/**
			 * Save group
			 */
			$group->save();
		}
		return $result;
	}       
}