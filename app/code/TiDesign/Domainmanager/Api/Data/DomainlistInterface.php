<?php

namespace TiDesign\Domainmanager\Api\Data;

interface DomainlistInterface
{
	const TD_ID				= 'td_id';
	const TD_STATUS			= 'td_status';
	const TD_STORE			= 'td_store';
	const TD_NAME			= 'td_name';
	const TD_URL			= 'td_url';
	const TD_DATE			= 'td_date';
	
	
	public function getTdId();
	
	public function getTdStatus();
	
	public function getTdStore();
	
	public function getTdName();
	
	public function getTdUrl();
	
	public function getTdDate();
	


	public function setTdId($td_id);
	
	public function setTdStatus($td_status);
	
	public function setTdStore($td_store);
	
	public function setTdName($td_name);
	
	public function setTdUrl($td_url);
	
	public function setTdDate($td_date);
}	