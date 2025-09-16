<?php

namespace TiDesign\Fundraiser\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ThemeCustomer extends AbstractDb
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'fundraiser_theme_customer_resource_model';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('fundraiser_theme_customer', 'id');
    }
}
