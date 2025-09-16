<?php
namespace TiDesign\CheckoutAgreements\Model\ResourceModel\Log;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('TiDesign\CheckoutAgreements\Model\Log', 'TiDesign\CheckoutAgreements\Model\ResourceModel\Log');
    }
}