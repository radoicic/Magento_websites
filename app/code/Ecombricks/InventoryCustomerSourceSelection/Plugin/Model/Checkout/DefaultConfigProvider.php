<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Checkout;

/**
 * Checkout default configuration provider plugin
 */
class DefaultConfigProvider extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Shipping method manager
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\ShippingMethodManagementInterface
     */
    private $shippingMethodManager;

    /**
     * Get current sources
     * 
     * @var \Ecombricks\InventoryCommon\Model\GetCurrentSources 
     */
    private $getCurrentSources;

    /**
     * Quote wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\QuoteFactory 
     */
    private $quoteWrapperFactory;

    /**
     * Checkout session
     * 
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * Quote
     * 
     * @var \Magento\Quote\Api\Data\CartInterface
     */
    private $quote;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\ShippingMethodManagementInterface $shippingMethodManager
     * @param \Ecombricks\InventoryCommon\Model\GetCurrentSources $getCurrentSources
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\QuoteFactory $quoteWrapperFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\ShippingMethodManagementInterface $shippingMethodManager,
        \Ecombricks\InventoryCommon\Model\GetCurrentSources $getCurrentSources,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\QuoteFactory $quoteWrapperFactory,
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        parent::__construct($wrapperFactory);
        $this->shippingMethodManager = $shippingMethodManager;
        $this->getCurrentSources = $getCurrentSources;
        $this->quoteWrapperFactory = $quoteWrapperFactory;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * After get configuration
     * 
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param array|mixed $result
     * @return array|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetConfig(
        \Magento\Checkout\Model\DefaultConfigProvider $subject,
        $result
    )
    {
        $result['selectedShippingMethod'] = $this->getSelectedShippingMethods();
        $result['quoteData']['sources'] = $this->getSources();
        return $result;
    }

    /**
     * Get active quote
     *
     * @return \Magento\Quote\Api\Data\CartInterface
     */
    private function getQuote(): \Magento\Quote\Api\Data\CartInterface
    {
        if ($this->quote !== null) {
            return $this->quote;
        }
        return $this->quote = $this->checkoutSession->getQuote();
    }

    /**
     * Get sources
     * 
     * @return array
     */
    private function getSources(): array
    {
        $sources = [];
        $sourceCodes = $this->quoteWrapperFactory->create($this->getQuote())->getSourceCodes();
        foreach ($this->getCurrentSources->execute() as $source) {
            $sourceCode = $source->getSourceCode();
            if (!in_array($sourceCode, $sourceCodes)) {
                continue;
            }
            $sources[] = [
                'source_code' => $sourceCode,
                'name' => $source->getName(),
            ];
        }
        return $sources;
    }

    /**
     * Get selected shipping methods
     * 
     * @return array
     */
    private function getSelectedShippingMethods(): array
    {
        $output = [];
        try {
            $quoteId = $this->getQuote()->getId();
            $shippingMethods = $this->shippingMethodManager->getMultiple($quoteId);
            if (empty($shippingMethods)) {
                return $output;
            }
            foreach ($shippingMethods as $shippingMethod) {
                $output[] = $shippingMethod->__toArray();
            }
        } catch (\Exception $exception) {
            $output = [];
        }
        return $output;
    }
}