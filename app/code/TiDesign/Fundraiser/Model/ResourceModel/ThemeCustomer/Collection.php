<?php

namespace TiDesign\Fundraiser\Model\ResourceModel\ThemeCustomer;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use TiDesign\Fundraiser\Model\ResourceModel\ThemeCustomer as ResourceModel;
use TiDesign\Fundraiser\Model\ThemeCustomer as Model;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'fundraiser_theme_customer_collection';

    /**
     * Initialize collection model.
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
