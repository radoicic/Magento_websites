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
 * @copyright  Copyright (c) 2013 Iksanika llc. (http://www.iksanika.com)
 * @license    http://www.iksanika.com/products/IKS-LICENSE.txt
 */

namespace Iksanika\Ordermanage\Model\System\Config\Source\Columns;

class Country extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Select
{

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter $converter
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter $converter,
        \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection,
        array $data = []
    ) {
        $this->_converter = $converter;
        parent::__construct($context, $converter, $data);
        $this->_countryCollection = $countryCollection;
    }
    
    protected function _getOptions()
    {
        $options = $this->_countryCollection->load()->toOptionArray();
//        array_unshift($options, array('value'=>'', 'label'=>Mage::helper('customer')->__('All countries')));
        return $options;
    }
}