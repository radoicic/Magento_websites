<?php

namespace TiDesign\Fundraiser\Block\Customer;

class Orders extends \Magento\Sales\Block\Order\History
{
    protected $_template = 'TiDesign_Fundraiser::customer/orders.phtml';

    /**
     * Get customer orders
     *
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrders()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->orders) {
            $this->orders = $this->_orderCollectionFactory->create();
            $this->orders->addFieldToSelect('*');
            $this->orders->getSelect()->joinInner(
                ['fundraiser_order' => $this->orders->getTable('fundraiser_order')],
                "main_table.entity_id = fundraiser_order.order_id AND fundraiser_order.customer_id = $customerId"
            );
            $this->orders->setOrder('created_at', 'desc');
        }
        return $this->orders;
    }

    /**
     * Get order view URL
     *
     * @param object $order
     * @return string
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('fundraiser/customer_order/view', ['order_id' => $order->getId()]);
    }

    /**
     * Get message for no orders.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getEmptyOrdersMessage()
    {
        return __('You have no fundraiser orders.');
    }
}
