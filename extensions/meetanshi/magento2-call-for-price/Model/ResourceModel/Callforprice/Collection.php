<?php

namespace Meetanshi\Callforprice\Model\ResourceModel\Callforprice;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Meetanshi\Callforprice\Model\Callforprice as CallforpriceModel;
use Meetanshi\Callforprice\Model\ResourceModel\Callforprice as CallforpriceResourceModel;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'meetanshi_callforprice_collection';
    protected $_eventObject = 'meetanshi_callforprice_collection';

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(
            CallforpriceModel::class,
            CallforpriceResourceModel::class
        );
    }
}
