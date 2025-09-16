<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Block\Checkout\Onepage;

/**
 * Checkout onepage failure block wrapper
 */
class Failure extends \Ecombricks\Common\DataObject\Wrapper implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * Quote configuration
     * 
     * @var \Ecombricks\Common\DataObject\WrapperFactory
     */
    private $quoteConfig;

    /**
     * Checkout session
     * 
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config $quoteConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config $quoteConfig,
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        parent::__construct($objectReflectionFactory);
        $this->quoteConfig = $quoteConfig;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Check if is split order
     * 
     * @return bool
     */
    public function isSplitOrder(): bool
    {
        return $this->quoteConfig->isSplitOrder();
    }

    /**
     * Get real order IDs
     * 
     * @return array|null
     */
    public function getRealOrderIds(): ?array
    {
        return $this->checkoutSession->getLastRealOrderIds();
    }
}