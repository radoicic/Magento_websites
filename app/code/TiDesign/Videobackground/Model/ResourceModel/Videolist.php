<?php
namespace TiDesign\Videobackground\Model\ResourceModel;

class Videolist extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	protected $_date;
	
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context,
		\Magento\Framework\Stdlib\DateTime\DateTime $date
	)
	{
		parent::__construct($context);
		$this->_date = $date;
	}
	
	protected function _construct()
	{
		$this->_init('tidesign_videobackground', 'id');
	}
	
	protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
	{
        if ($object->isObjectNew()) {
            $object->setCreateDate($this->_date->date());
        }
        return parent::_beforeSave($object);
	}
}