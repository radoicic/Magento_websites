<?php

namespace TiDesign\Fundraiser\Model;

use Magento\Framework\Model\AbstractModel;
use TiDesign\Fundraiser\Model\ResourceModel\FundraiserOrderItem as ResourceModel;

class FundraiserOrder extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'fundraiser_order_model';

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
