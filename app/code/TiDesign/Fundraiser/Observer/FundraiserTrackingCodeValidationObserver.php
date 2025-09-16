<?php

namespace TiDesign\Fundraiser\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadata;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use TiDesign\Fundraiser\Helper\Config;
use TiDesign\Fundraiser\Model\Tracking\GetCustomerIdByTrackingCode;

class FundraiserTrackingCodeValidationObserver implements ObserverInterface
{
    /**
     * @param Config $configHelper
     * @param CookieManagerInterface $cookieManager
     * @param GetCustomerIdByTrackingCode $getCustomerIdByTrackingCode
     * @param CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        protected Config $configHelper,
        protected CookieManagerInterface $cookieManager,
        protected GetCustomerIdByTrackingCode $getCustomerIdByTrackingCode,
        protected CookieMetadataFactory $cookieMetadataFactory
    ) {
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $cookieName = $this->configHelper->getTrackingCodeCookieName();
        if ($cookieName && $trackingCode = $this->cookieManager->getCookie($cookieName)) {
            $trackingCode = urldecode($trackingCode);
        }
        if (isset($trackingCode) && !$this->getCustomerIdByTrackingCode->execute($trackingCode)) {
            $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
            $metadata->setPath('/');
            $this->cookieManager->deleteCookie($cookieName, $metadata);
        }
    }
}
