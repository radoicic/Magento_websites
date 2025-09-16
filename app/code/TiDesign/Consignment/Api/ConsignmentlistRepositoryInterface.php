<?php
namespace TiDesign\Consignment\Api;

interface ConsignmentlistRepositoryInterface
{
	public function save(\TiDesign\Consignment\Api\Data\ConsignmentlistInterface $consignmentlist);
	
	public function getById($consignmentlistId);
	
	public function delete(\TiDesign\Consignment\Api\Data\ConsignmentlistInterface $consignmentlist);

	public function deleteById($consignmentlistId);
	
	public function getList();
}