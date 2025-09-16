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

class Sku extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Text
{
    function getCondition()
    {
        if(trim($this->getValue())=='')
            return null;
        $skuIds = explode(',', $this->getValue());
        $skuIdsArray = array();
        foreach($skuIds as $skuId)
            $skuIdsArray[] = trim($skuId);
        if(count($skuIdsArray) == 1)
        {
            $likeExpression = $this->_resourceHelper->addLikeEscape($this->getValue(), array('position' => 'any'));
            return array('like' => $likeExpression);
        }
        else
            return array('inset' => $skuIdsArray);
    }
    
}
