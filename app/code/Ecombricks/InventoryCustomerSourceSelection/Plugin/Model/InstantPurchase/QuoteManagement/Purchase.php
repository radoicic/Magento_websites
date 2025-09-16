<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\InstantPurchase\QuoteManagement;

/**
 * Quote management purchase plugin
 */
class Purchase
{
    /**
     * Quote repository
     * 
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * Quote management
     * 
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    private $quoteManagement;

    /**
     * Quote configuration
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config
     */
    private $quoteConfig;

    /**
     * Constructor
     * 
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Api\CartManagementInterface $quoteManagement
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config $quoteConfig
     * @return void
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Api\CartManagementInterface $quoteManagement,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Quote\Config $quoteConfig
    )
    {
        $this->quoteRepository = $quoteRepository;
        $this->quoteManagement = $quoteManagement;
        $this->quoteConfig = $quoteConfig;
    }

    /**
     * Around purchase
     * 
     * @param \Magento\InstantPurchase\Model\QuoteManagement\Purchase $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote $quote
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundPurchase(
        \Magento\InstantPurchase\Model\QuoteManagement\Purchase $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote $quote
    ): int
    {
        if (!$this->quoteConfig->isSplitOrder()) {
            return $proceed($quote);
        }
        $quote->collectTotals();
        $this->quoteRepository->save($quote);
        return (int) current($this->quoteManagement->placeOrder($quote->getId()));
    }
}