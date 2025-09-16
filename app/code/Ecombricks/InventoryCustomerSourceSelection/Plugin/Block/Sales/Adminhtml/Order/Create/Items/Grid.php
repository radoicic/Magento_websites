<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Block\Sales\Adminhtml\Order\Create\Items;

/**
 * Create order items grid plugin
 */
class Grid extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Get current sources
     * 
     * @var \Ecombricks\InventoryCommon\Model\GetCurrentSources 
     */
    private $getCurrentSources;

    /**
     * Stock state
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Api\CatalogInventory\StockStateInterface 
     */
    private $stockState;
    
    /**
     * Quote item wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory
     */
    private $quoteItemWrapperFactory;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCommon\Model\GetCurrentSources $getCurrentSources
     * @param \Ecombricks\InventoryCustomerSourceSelection\Api\CatalogInventory\StockStateInterface $stockState
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory $quoteItemWrapperFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCommon\Model\GetCurrentSources $getCurrentSources,
        \Ecombricks\InventoryCustomerSourceSelection\Api\CatalogInventory\StockStateInterface $stockState,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\ItemFactory $quoteItemWrapperFactory
    )
    {
        parent::__construct($wrapperFactory);
        $this->getCurrentSources = $getCurrentSources;
        $this->stockState = $stockState;
        $this->quoteItemWrapperFactory = $quoteItemWrapperFactory;
    }

    /**
     * Around get data
     * 
     * @param \Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid $subject
     * @param \Closure $proceed
     * @param string $key
     * @param string|int $index
     * @return mixed
     */
    public function aroundGetData(
        \Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid $subject,
        \Closure $proceed,
        $key = '',
        $index = null
    )
    {
        $this->setSubject($subject);
        if ($key === 'sources') {
            $storeId = (int) $subject->getStore()->getId();
            return $this->getCurrentSources->execute($storeId);
        }
        return $proceed($key, $index);
    }
    
    /**
     * Around get items
     * 
     * @param \Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid $subject
     * @param \Closure $proceed
     * @return \Magento\Quote\Model\Quote\Item[]
     */
    public function aroundGetItems(
        \Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid $subject,
        \Closure $proceed
    )
    {
        $this->setSubject($subject);
        $items = $subject->getParentBlock()->getItems();
        $quote = $subject->getQuote();
        $oldSuperMode = $quote->getIsSuperMode();
        $quote->setIsSuperMode(false);
        $stockState = $this->getSubjectPropertyValue('stockState');
        foreach ($items as $item) {
            $product = $item->getProduct();
            $sourceCode = (string) $this->quoteItemWrapperFactory->create($item)->getSourceCode();
            $qty = $item->getQty();
            $item->setQty($qty);
            if (!$item->getMessage()) {
                foreach ($this->getQuoteItemProductIds($item) as $productId) {
                    $check = $this->stockState->checkQuoteItemSourceQty($productId, $sourceCode, $qty, $qty, $qty);
                    $item->setMessage($check->getMessage());
                    $item->setHasError($check->getHasError());
                }
            }
            if ($product->getStatus() == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED) {
                $item->setMessage($subject->escapeHtml(__('This product is disabled.')));
                $item->setHasError(true);
            }
        }
        $quote->setIsSuperMode($oldSuperMode);
        return $items;
    }

    /**
     * Get quote item product IDs
     * 
     * @param \Magento\Quote\Api\Data\CartItemInterface $item
     * @return array
     */
    private function getQuoteItemProductIds($item): array
    {
        $productIds = [];
        $childItems = $item->getChildren();
        if (count($childItems)) {
            foreach ($childItems as $childItem) {
                $productIds[] = $childItem->getProduct()->getId();
            }
        } else {
            $productIds[] = $item->getProduct()->getId();
        }
        return $productIds;
    }
}