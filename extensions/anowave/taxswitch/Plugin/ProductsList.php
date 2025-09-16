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
 
namespace Anowave\TaxSwitch\Plugin;

class ProductsList
{
    /**
     * After get cache key info 
     * 
     * @param \Magento\CatalogWidget\Block\Product\ProductsList $list
     * @param array $result
     * 
     * @return [type]
     */
    public function afterGetCacheKeyInfo(\Magento\CatalogWidget\Block\Product\ProductsList $list, array $result = [])
    {
        $result[] = \Magento\Framework\App\ObjectManager::getInstance()->create('Anowave\TaxSwitch\Helper\Data')->getCurrentTaxDisplay();

        return $result;   
    }
}
  