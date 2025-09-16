<?php
namespace TiDesign\CheckoutAgreements\Model\ResourceModel\Customerlist;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('TiDesign\CheckoutAgreements\Model\Customerlist', 'TiDesign\CheckoutAgreements\Model\ResourceModel\Customerlist');
    }
}