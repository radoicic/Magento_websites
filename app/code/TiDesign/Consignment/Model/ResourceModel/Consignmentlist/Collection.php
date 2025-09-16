<?php

namespace TiDesign\Consignment\Model\ResourceModel\Consignmentlist;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

	protected $_idFieldName = 'td_id';
    
    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
		$this->_init(
			'TiDesign\Consignment\Model\Consignmentlist',
			'TiDesign\Consignment\Model\ResourceModel\Consignmentlist');
    }
}