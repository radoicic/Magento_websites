<?php

namespace TiDesign\Domainmanager\Api;

interface DomainlistRepositoryInterface
{
	public function save(\TiDesign\Domainmanager\Api\Data\DomainlistInterface $domainlist);
	
	public function getById($domainlistId);
	
	public function delete(\TiDesign\Domainmanager\Api\Data\DomainlistInterface $domainlist);

	public function deleteById($domainlistId);
	
	public function getList();
}