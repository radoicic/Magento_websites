<?php

namespace TiDesign\Videobackground\Model\ResourceModel\Videolist;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

	protected $_idFieldName = 'id';
    
    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
		$this->_init(
			'TiDesign\Videobackground\Model\Videolist',
			'TiDesign\Videobackground\Model\ResourceModel\Videolist');
    }
}