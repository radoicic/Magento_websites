<?php
namespace TiDesign\BundleProduct\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends AbstractHelper
{
	protected $scopeConfig;
	
	public function __construct(
        Context 				$context,
		ScopeConfigInterface 	$scopeConfig
	){
		$this->scopeConfig 		= $scopeConfig;
        parent::__construct($context);
    }	
	
	public function getConfig($config_path)
    {
		return $this->scopeConfig->getValue(
            $config_path,
            ScopeInterface::SCOPE_STORE
		);
    }
}