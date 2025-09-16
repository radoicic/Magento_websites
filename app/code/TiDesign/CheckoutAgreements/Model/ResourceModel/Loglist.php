<?php
namespace TiDesign\CheckoutAgreements\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Loglist extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('tidesign_checkoutagreements_loglist', 'loglist_id');
    }
}