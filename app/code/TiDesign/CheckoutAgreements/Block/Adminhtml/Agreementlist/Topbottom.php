<?php
namespace TiDesign\CheckoutAgreements\Block\Adminhtml\Agreementlist;

use Magento\Backend\Block\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Topbottom extends Template
{
	const CONTENT_HEIGHT 	= 'checkoutagreements/checkoutagreements_status/content_height';
    const BUTTON_TEXT 		= 'checkoutagreements/checkoutagreements_status/button_text';
	private 	$scopeConfig;
    /**
    * @param Context $context
    * @param array $data
    */
    public function __construct(
        Template\Context $context,
		ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
		$this->scopeConfig 		= $scopeConfig;
        parent::__construct($context, $data);
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	public function getContentHeight(){
		return $this->getConfig(self::CONTENT_HEIGHT);
	}
	public function getButtonText(){
		return $this->getConfig(self::BUTTON_TEXT);
	}
}