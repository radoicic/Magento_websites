<?php
namespace TiDesign\CheckoutAgreements\Controller\Confirm;

use Magento\Framework\Controller\ResultFactory;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Index extends \Magento\Framework\App\Action\Action
{

	protected $_dir;
	protected $_filesystem;
	protected $_pageFactory;
	protected $_dataFactory;
	protected $_logFactory;
	protected $_remoteAddress;
	protected $_timezoneInterface;
	protected $_mailHelper;
	protected $_request;

	public function __construct(
		\Magento\Framework\App\Action\Context 			$context,
		\Magento\Framework\View\Result\PageFactory 		$pageFactory,
		\TiDesign\CheckoutAgreements\Model\DataFactory 	$dataFactory,
		\TiDesign\CheckoutAgreements\Model\LogFactory 	$logFactory,
		\TiDesign\CheckoutAgreements\Helper\Mailer	 	$mailHelper,
		\Magento\Framework\Filesystem 					$filesystem,
		\Magento\Framework\App\Request\Http 			$request,
		DirectoryList 									$dir,
		RemoteAddress									$remoteAddress,
		TimezoneInterface 								$timezoneInterface
	){
		$this->_pageFactory 		= $pageFactory;
		$this->_dataFactory 		= $dataFactory;
		$this->_logFactory 			= $logFactory;
		$this->_dir 				= $dir;
		$this->_filesystem 			= $filesystem;
		$this->_request 			= $request;
		$this->_remoteAddress 		= $remoteAddress;
		$this->_timezoneInterface 	= $timezoneInterface;
		$this->_mailHelper 			= $mailHelper;
		return parent::__construct($context);
	}

    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

}