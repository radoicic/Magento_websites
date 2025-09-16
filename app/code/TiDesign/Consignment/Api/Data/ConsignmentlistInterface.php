<?php
namespace TiDesign\Consignment\Api\Data;

interface ConsignmentlistInterface
{
	const TD_ID				= 'td_id';
	const TD_STATUS			= 'td_status';
	const TD_NAME			= 'td_name';
	const TD_WEBSITE		= 'td_website';
	const TD_CATEGORY		= 'td_category';
	const TD_DATE			= 'td_date';
	const TD_DISABLED		= 'td_disabled';
	
	
	public function getTdId();
	public function getTdStatus();
	public function getTdName();
	public function getTdWebsite();
	public function getTdCategory();
	public function getTdDate();
	public function getTdDisabled();
	

	public function setTdId($td_id);
	public function setTdStatus($td_status);
	public function setTdName($td_name);
	public function setTdWebsite($td_website);
	public function setTdCategory($td_category);
	public function setTdDate($td_date);
	public function setTdDisabled($td_disabled);
}	