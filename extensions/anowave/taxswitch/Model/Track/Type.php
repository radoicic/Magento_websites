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
namespace Anowave\TaxSwitch\Model\Track;
 
class Type
{
	const TYPE_UA 	= 'ua';
	const TYPE_GTM 	= 'gtm';
	
	/**
	 * @return []
	 */
	public function toOptionArray()
	{
		return
		[
			[
				'value' => self::TYPE_UA,
				'label' => __('Universal Analytics')
			],
			[
				'value' => self::TYPE_GTM,
				'label' => __('Google Tag Manager')
			]
		];
	}
}