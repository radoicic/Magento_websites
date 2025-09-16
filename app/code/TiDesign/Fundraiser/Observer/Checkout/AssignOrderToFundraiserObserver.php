<?php

namespace TiDesign\Fundraiser\Observer\Checkout;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use TiDesign\Fundraiser\Model\Catalog\Product\GetCategoryIdsByProduct;
use TiDesign\Fundraiser\Model\ResourceModel\FundraiserOrder as FundraiserOrderResource;
use TiDesign\Fundraiser\Model\ResourceModel\FundraiserOrderItem as FundraiserOrderItemResource;
use TiDesign\Fundraiser\Model\ThemeCustomer\GetThemesByFundraiserCategoryIds;

class AssignOrderToFundraiserObserver implements ObserverInterface
{
    /**
     * @param GetThemesByFundraiserCategoryIds $getThemesByFundraiserCategoryIds
     * @param FundraiserOrderResource $fundraiserOrderResource
     * @param FundraiserOrderItemResource $fundraiserOrderItemResource
     * @param GetCategoryIdsByProduct $getCategoryIdsByProduct
     */
    public function __construct(
        protected GetThemesByFundraiserCategoryIds $getThemesByFundraiserCategoryIds,
        protected FundraiserOrderResource $fundraiserOrderResource,
        protected FundraiserOrderItemResource $fundraiserOrderItemResource,
        protected GetCategoryIdsByProduct $getCategoryIdsByProduct
    ) {
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getData('quote');

        $itemIdToCategoryIds = $this->collectQuoteItemsCategoryIds($quote);
        $categoryIds = array_merge(...array_values($itemIdToCategoryIds));
        $themes = $this->getThemesByFundraiserCategoryIds->execute($categoryIds);
        $itemIdToFundraiserData = $this->consolidateQuoteItemIdToFundraiserData($itemIdToCategoryIds, $themes);
        $this->allocateOrderItemIdToCustomer($order, $itemIdToFundraiserData);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return array
     */
    private function collectQuoteItemsCategoryIds($quote)
    {
        return array_reduce($quote->getAllVisibleItems(), function ($carry, $item) {
            /** @var \Magento\Quote\Model\Quote\Item $item */
            $categoryIds = $this->getCategoryIdsByProduct->execute($item->getProduct());
            $carry[$item->getItemId()] =  $categoryIds;
            return $carry;
        }, []);
    }

    /**
     * @param array $itemIdToCategoryIds
     * @param $themeCustomers
     * @return array
     */
    private function consolidateQuoteItemIdToFundraiserData($itemIdToCategoryIds, $themeCustomers)
    {
        return array_reduce($themeCustomers, function ($carry, $themeCustomer) use ($itemIdToCategoryIds) {
            $customerCategoryIds = $themeCustomer->getCategoryIds();
            foreach ($itemIdToCategoryIds as $itemId => $categoryIds) {
                if (array_intersect($customerCategoryIds, $categoryIds) && !isset($carry[$itemId])) {
                    $carry[$itemId] = [
                        'customer_id' => $themeCustomer->getCustomerId(),
                        'fundraiser_theme_customer_id' => $themeCustomer->getId()
                    ];
                }
            }
            return $carry;
        }, []);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param array $itemIdToFundraiserData
     * @return void
     */
    private function allocateOrderItemIdToCustomer($order, $itemIdToFundraiserData)
    {
        if (!$itemIdToFundraiserData) {
            return;
        }
        $orderItemIdToOrderItems = array_reduce($order->getItems(), function ($carry, $item) {
            $carry[$item->getQuoteItemId()] = $item;
            return $carry;
        }, []);
        $orderId = $order->getId();
        $fundraiserOrderData = [];
        $fundraiserOrderItemData = [];
        foreach ($itemIdToFundraiserData as $quoteItemId => $data) {
            $customerId = $data['customer_id'];
            $orderData = array_merge($data, ['order_id' => $orderId]);
            $key = implode('_', [$customerId, $orderId]);
            $fundraiserOrderData[$key] = $orderData;
            $orderItem = $orderItemIdToOrderItems[$quoteItemId];
            $itemData = array_merge($data, ['order_item_id' => $orderItem->getId()]);
            $key = implode('_', [$customerId, $orderItem->getId()]);
            $fundraiserOrderItemData[$key] = $itemData;
        }
        if ($fundraiserOrderData) {
            $connection = $this->fundraiserOrderResource->getConnection();
            $connection->insertMultiple($this->fundraiserOrderResource->getMainTable(), $fundraiserOrderData);
            $connection->insertMultiple($this->fundraiserOrderItemResource->getMainTable(), $fundraiserOrderItemData);
        }
    }
}
