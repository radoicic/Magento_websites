<?php
namespace TiDesign\CheckoutAgreements\Model\ResourceModel\Data;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('TiDesign\CheckoutAgreements\Model\Data', 'TiDesign\CheckoutAgreements\Model\ResourceModel\Data');
    }
}