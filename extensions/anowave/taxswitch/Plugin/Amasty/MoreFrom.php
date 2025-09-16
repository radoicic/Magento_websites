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
 
namespace Anowave\TaxSwitch\Plugin\Amasty;

use Anowave\TaxSwitch\Plugin\Base;

class MoreFrom extends Base
{
    /**
     * Modify cache key info 
     * 
     * @param \Amasty\ShopbyBrand\Block\Catalog\Product\ProductList\MoreFrom $block
     * @param array $key
     * 
     * @return [type]
     */
    public function afterGetCacheKeyInfo(\Amasty\ShopbyBrand\Block\Catalog\Product\ProductList\MoreFrom $block, array $key = [])
    {
        $key[] = $this->helper->getCurrentTaxDisplay();

        return $key;
    }

    /**
     * Modify identities
     * 
     * @param \Amasty\ShopbyBrand\Block\Catalog\Product\ProductList\MoreFrom $block
     * @param array $identities
     * 
     * @return [type]
     */
    public function afterGetIdentities(\Amasty\ShopbyBrand\Block\Catalog\Product\ProductList\MoreFrom $block, array $identities = [])
    {
        $identities['tax'] = $this->helper->getCurrentTaxDisplay();

        return $identities;
    }
}