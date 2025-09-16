<?php

namespace TiDesign\Fundraiser\Model;

use Magento\Framework\Model\AbstractModel;
use TiDesign\Fundraiser\Model\ResourceModel\FundraiserOrder as ResourceModel;

class FundraiserOrderItem extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'fundraiser_order_item_model';

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
