<?php

namespace TiDesign\Videobackground\Api;

interface VideolistRepositoryInterface
{
	public function save(\TiDesign\Videobackground\Api\Data\VideolistInterface $videolist);
	
	public function getById($videolistId);
	
	public function delete(\TiDesign\Videobackground\Api\Data\VideolistInterface $videolist);

	public function deleteById($videolistId);
	
	public function getList();
}