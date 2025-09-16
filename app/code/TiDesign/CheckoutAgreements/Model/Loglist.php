<?php
namespace TiDesign\CheckoutAgreements\Model;

use TiDesign\CheckoutAgreements\Api\Data\LoglistInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

class Loglist extends AbstractModel implements LoglistInterface, IdentityInterface
{
 
	const NOROUTE_ENTITY_ID = 'no-route';
	
	
    protected function _construct()
    {
        $this->_init('TiDesign\CheckoutAgreements\Model\ResourceModel\Loglist');
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
        return [$this->getLogId()];
    } 
 
 	public function getLogId()
	{
		return parent::getData(self::TD_ID);
	}
 	public function getCustomer()
	{
		return parent::getData(self::TD_CUSTOMER);
	}
 	public function getMessage()
	{
		return parent::getData(self::TD_MESSAGE);
	}
 	public function getDate()
	{
		return parent::getData(self::TD_DATE);
	}
 	public function getCreationIp()
	{
		return parent::getData(self::TD_IP);
	}
 	public function getFile()
	{
		return parent::getData(self::TD_FILE);
	}
 	public function getOperation()
	{
		return parent::getData(self::TD_OPERATION);
	}

	
 
	
	public function setLogId($log_id)
	{
		return $this->setData(self::TD_ID, $log_id);
	}
	public function setCustomer($customer_id)
	{
		return $this->setData(self::TD_CUSTOMER, $customer_id);
	}
	public function setMessage($message)
	{
		return $this->setData(self::TD_MESSAGE, $message);
	}
	public function setDate($date)
	{
		return $this->setData(self::TD_DATE, $date);
	}
	public function setCreationIp($creation_ip)
	{
		return $this->setData(self::TD_IP, $creation_ip);
	}
 	public function setFile($filename)
	{
		return $this->setData(self::TD_FILE, $filename);
	}
 	public function setOperation($operation)
	{
		return $this->setData(self::TD_OPERATION, $operation);
	}
 
}