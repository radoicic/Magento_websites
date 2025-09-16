<?php
namespace TiDesign\CheckoutAgreements\Api\Data;

interface LoglistInterface
{
	const TD_ID				= 'log_id';
	const TD_CUSTOMER		= 'customer_id';
	const TD_MESSAGE		= 'message';
	const TD_DATE			= 'date';
	const TD_IP				= 'creation_ip';
	const TD_FILE			= 'filename';
	const TD_OPERATION		= 'operation';

	
	
	public function getLogId();
	public function getCustomer();
	public function getMessage();
	public function getDate();
	public function getCreationIp();
	public function getFile();
	public function getOperation();
	

	public function setLogId($log_id);
	public function setCustomer($customer_id);
	public function setMessage($message);
	public function setDate($date);
	public function setCreationIp($creation_ip);
	public function setFile($filename);
	public function setOperation($operation);
}
