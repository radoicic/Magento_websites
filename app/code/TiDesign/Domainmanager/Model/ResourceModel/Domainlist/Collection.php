<?php

namespace TiDesign\Domainmanager\Model\ResourceModel\Domainlist;

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
			'TiDesign\Domainmanager\Model\Domainlist',
			'TiDesign\Domainmanager\Model\ResourceModel\Domainlist');
    }
}