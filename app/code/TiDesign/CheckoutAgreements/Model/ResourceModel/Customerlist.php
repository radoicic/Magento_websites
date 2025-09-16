<?php
namespace TiDesign\CheckoutAgreements\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Customerlist extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('tidesign_consignment_checkoutagreements_list', 'entity_id');
    }
}