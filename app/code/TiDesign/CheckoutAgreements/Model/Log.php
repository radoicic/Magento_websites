<?php
namespace TiDesign\CheckoutAgreements\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Log extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('TiDesign\CheckoutAgreements\Model\ResourceModel\Log');
    }
}