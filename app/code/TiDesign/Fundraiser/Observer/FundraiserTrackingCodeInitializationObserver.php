<?php

namespace TiDesign\Fundraiser\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use TiDesign\Fundraiser\Helper\Config as ConfigHelper;
use Magento\Framework\App\RequestInterface;

class FundraiserTrackingCodeInitializationObserver implements ObserverInterface
{
    /**
     * @param ConfigHelper $configHelper
     * @param RequestInterface $request
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        protected ConfigHelper $configHelper,
        protected RequestInterface $request,
        protected \Magento\Checkout\Model\Session $checkoutSession,
        protected CartRepositoryInterface $quoteRepository
    ) {
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        if (!$this->configHelper->isEnabled() || !($trackingCodeParam = $this->configHelper->getTrackingCodeParam())) {
            return;
        }
        if (!($trackingCode = $this->request->getParam($trackingCodeParam))) {
            return;
        }
        $quote = $this->checkoutSession->getQuote();
        if ($quote->getData('fundraiser_tracking_code') !== $trackingCode) {
            $quote->setData('fundraiser_tracking_code', $trackingCode);
            $quote->removeAllItems();
            $this->quoteRepository->save($quote);
        }
    }
}
