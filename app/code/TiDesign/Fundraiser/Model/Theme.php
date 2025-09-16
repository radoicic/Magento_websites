<?php

namespace TiDesign\Fundraiser\Model;

use Magento\Framework\Model\AbstractModel;
use TiDesign\Fundraiser\Model\ResourceModel\Theme as ResourceModel;

class Theme extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'fundraiser_theme_model';

    /**
     * Initialize magento model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
