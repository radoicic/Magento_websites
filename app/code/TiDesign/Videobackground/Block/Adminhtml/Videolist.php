<?php

namespace TiDesign\Videobackground\Block\Adminhtml;

class Videolist extends \Magento\Backend\Block\Widget\Grid\Container
{
	protected function _construct()
	{
		$this->_controller	= 'adminhtml_videolist';
		$this->_blockGroup	= 'TiDesign_Videobackground';
		$this->_headerText	= __('Manage Video Files');
		
		parent::_construct();
	}
	
	
}