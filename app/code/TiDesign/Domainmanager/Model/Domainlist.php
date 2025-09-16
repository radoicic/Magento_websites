<?php
namespace TiDesign\Domainmanager\Model;

use TiDesign\Domainmanager\Api\Data\DomainlistInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

class Domainlist extends AbstractModel implements DomainlistInterface, IdentityInterface
{
 
    protected function _construct()
    {
        $this->_init('TiDesign\Domainmanager\Model\ResourceModel\Domainlist');
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
	
 	public function getTdStore()
	{
		return parent::getData(self::TD_STORE);
	}
	
 	public function getTdName()
	{
		return parent::getData(self::TD_NAME);
	}
		
 	public function getTdUrl()
	{
		return parent::getData(self::TD_URL);
	}
	
 	public function getTdDate()
	{
		return parent::getData(self::TD_DATE);
	}
	
 
	
	public function setTdId($td_id)
	{
		return $this->setData(self::TD_ID, $td_id);
	}
		
	public function setTdStatus($td_status)
	{
		return $this->setData(self::TD_STATUS, $td_status);
	}
	
	public function setTdStore($td_store)
	{
		return $this->setData(self::TD_STORE, $td_store);
	}
	
	public function setTdName($td_name)
	{
		return $this->setData(self::TD_NAME, $td_name);
	}
	
	public function setTdUrl($td_url)
	{
		return $this->setData(self::TD_URL, $td_url);
	}
	
 	public function setTdDate($td_date)
	{
		return $this->setData(self::TD_DATE, $td_date);
	}
	
}