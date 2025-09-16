<?php
namespace TiDesign\Consignment\Model;

use TiDesign\Consignment\Api\Data\ConsignmentlistInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

class Consignmentlist extends AbstractModel implements ConsignmentlistInterface, IdentityInterface
{
 
	const NOROUTE_ENTITY_ID = 'no-route';
	
	
    protected function _construct()
    {
        $this->_init('TiDesign\Consignment\Model\ResourceModel\Consignmentlist');
    }
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRoute();
        }
        return parent::load($id, $field);
    }
    
    public function noRoute()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }	
	
	
    public function getIdentities()
    {
        return [$this->getTdId()];
    } 
 
 	public function getTdId()
	{
		return parent::getData(self::TD_ID);
	}
	
 	public function getTdStatus()
	{
		return parent::getData(self::TD_STATUS);
	}	
		
 	public function getTdName()
	{
		return parent::getData(self::TD_NAME);
	}
 	public function getTdWebsite()
	{
		return parent::getData(self::TD_WEBSITE);
	}
		
 	public function getTdCategory()
	{
		return parent::getData(self::TD_CATEGORY);
	}
	
 	public function getTdDate()
	{
		return parent::getData(self::TD_DATE);
	}	
 	public function getTdDisabled()
	{
		return parent::getData(self::TD_DISABLED);
	}
	
 
	
	public function setTdId($td_id)
	{
		return $this->setData(self::TD_ID, $td_id);
	}
		
	public function setTdStatus($td_status)
	{
		return $this->setData(self::TD_STATUS, $td_status);
	}
		
	public function setTdName($td_name)
	{
		return $this->setData(self::TD_NAME, $td_name);
	}
	public function setTdWebsite($td_website)
	{
		return $this->setData(self::TD_WEBSITE, $td_website);
	}
	
	public function setTdCategory($td_category)
	{
		return $this->setData(self::TD_CATEGORY, $td_category);
	}
	
 	public function setTdDate($td_date)
	{
		return $this->setData(self::TD_DATE, $td_date);
	}	
 	public function setTdDisabled($td_disabled)
	{
		return $this->setData(self::TD_DISABLED, $td_disabled);
	}
	
}