<?php

namespace TiDesign\PurchaseorderFix\Model;

use Magento\Checkout\Model\ConfigProviderInterface;


use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class AdditionalConfigVars implements ConfigProviderInterface
{
	protected 	$scopeConfig;
	private 	$storeManager;
	
	public function __construct(
		ScopeConfigInterface 	$scopeConfig,
		StoreManagerInterface 	$storeManager
	) {
		$this->scopeConfig      = $scopeConfig;
		$this->storeManager 	= $storeManager;
	}
	public function getConfig()
	{

		
		$storeId = $this->storeManager->getStore();
		$data = nl2br($this->scopeConfig->getValue(
            'payment/purchaseorder/instructions',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));		
		$additionalVariables['payment']['instructions']['purchaseorder'] = $data;
		return $additionalVariables;
	}
}