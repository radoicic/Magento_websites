<?php
namespace TiDesign\CheckoutAgreements\Model\ResourceModel\Loglist;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected $_idFieldName = 'loglist_id';

    protected function _construct()
    {
        $this->_init(
            \TiDesign\CheckoutAgreements\Model\Loglist::class,
            \TiDesign\CheckoutAgreements\Model\ResourceModel\Loglist::class
        );
    }
}