<?php

namespace Meetanshi\Callforprice\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Callforprice
 */
class Callforprice extends AbstractModel
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Callforprice::class);
    }
}
