<?php

namespace TiDesign\Fundraiser\Model\ResourceModel\FundraiserOrder;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use TiDesign\Fundraiser\Model\ResourceModel\FundraiserOrder as ResourceModel;
use TiDesign\Fundraiser\Model\FundraiserOrder as Model;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'fundraiser_order_collection';

    /**
     * Initialize collection model.
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
