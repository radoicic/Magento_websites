<?php

namespace TiDesign\Fundraiser\Model\ResourceModel\Theme;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use TiDesign\Fundraiser\Model\ResourceModel\Theme as ResourceModel;
use TiDesign\Fundraiser\Model\Theme as Model;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'fundraiser_theme_collection';

    /**
     * Initialize collection model.
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
