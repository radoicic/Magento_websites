<?php
/**
 * Anowave Magento 2 MaxMind API
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
namespace Anowave\TaxSwitch\Model\System\Config\Source;
 
class Template
{
	const AS_SELECT = 1;
	const AS_RADIOS = 2;
	const AS_TOGGLE = 3;
	/**
	 * Get option array 
	 * 
	 * @return array
	 */
	public function toOptionArray() : array
	{
		return
		[
			[
				'value' => static::AS_SELECT,
				'label' => __('Select dropdown')
			],
			[
				'value' => static::AS_RADIOS,
				'label' => __('Radio choice')
			],
		    [
		        'value' => static::AS_TOGGLE,
		        'label' => __('Toggle choice')
		    ]
		];
	}
}