<?php
/**
 * Iksanika llc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.iksanika.com/products/IKS-LICENSE.txt
 *
 * @category   Iksanika
 * @package    Iksanika_Ordermanage
 * @copyright  Copyright (c) 2015 Iksanika llc. (http://www.iksanika.com)
 * @license    http://www.iksanika.com/products/IKS-LICENSE.txt
 */
namespace Iksanika\Productmanage\Block\Widget\Grid\Column\Filter;

class Category extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Text
{
    function getCondition()
    {
        if(trim($this->getValue())=='')
            return null;
        $categoryIds         =   explode(',', $this->getValue());
        $categoryIdsArray    =   array();
        foreach($categoryIds as $skuId)
            $categoryIdsArray[] = trim($skuId);
        return array('inset' => $categoryIdsArray);
    }
    
}
