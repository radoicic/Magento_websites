<?php

namespace TiDesign\Fundraiser\Controller\Customer\Order;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class View extends \Magento\Framework\App\Action\Action implements HttpGetActionInterface
{
    /**
     * @param Context $context
     * @param CollectionFactory $orderCollectionFactory
     * @param Session $customerSession
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        protected CollectionFactory $orderCollectionFactory,
        protected Session $customerSession,
        protected Registry $registry
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $order = $this->getOrder();
        if (!$order || !$order->getId()) {
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/customer/orders');
        }

        $this->registry->register('current_order', $order);

        /** @var \Magento\Framework\View\Element\Html\Links $navigationBlock */
        $navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('fundraiser/customer/orders');
        }
        return $resultPage;
    }

    /**
     * @return \Magento\Framework\DataObject|null
     */
    private function getOrder()
    {
        $customerId = $this->customerSession->getCustomerId();
        $orderId = $this->getRequest()->getParam('order_id');
        if (!$customerId || !$orderId) {
            return null;
        }
        $collection = $this->orderCollectionFactory->create();
        $collection->getSelect()->joinInner(
            ['fundraiser_order' => $collection->getTable('fundraiser_order')],
            "main_table.entity_id = fundraiser_order.order_id AND fundraiser_order.customer_id = $customerId"
        );
        $collection->addFieldToFilter('main_table.entity_id', $orderId);
        return $collection->getFirstItem();
    }
}
