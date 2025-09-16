<?php
namespace TiDesign\Consignment\Cron;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\{ObjectManager, State};


class Cron {
	protected $_dataHelper;
	protected $_logger;
	
	public function __construct(
		\TiDesign\Consignment\Helper\Consignment $dataHelper,
		\TiDesign\Consignment\Logger\Logger $customLogger,
		State $state
	)
	{
		$this->_dataHelper 	= $dataHelper;	
		$this->_logger 		= $customLogger;
/*		
		try {
			$state->setAreaCode('adminhtml');
		} catch (\Magento\Framework\Exception\LocalizedException $e) {
			$this->_logger->error('Cron error -> ' .  $e->getMessage());
		}  
*/	
try {
	if (!$state->getAreaCode()) {
		$state->setAreaCode('adminhtml');
	}	
} catch (\Magento\Framework\Exception\LocalizedException $e) {
	$this->_logger->error('Cron error -> ' .  $e->getMessage());
}  	
	
	}
	public function execute() {
		try {
			$counter = $this->_dataHelper->assignCategoryAll();
		} catch (\Magento\Framework\Exception\LocalizedException $e) {
			$this->_logger->error('assignCategoryAll error -> ' .  $e->getMessage());
		}

	}
}