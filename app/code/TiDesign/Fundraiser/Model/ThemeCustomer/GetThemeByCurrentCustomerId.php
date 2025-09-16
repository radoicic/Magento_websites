<?php

namespace TiDesign\Fundraiser\Model\ThemeCustomer;

use TiDesign\Fundraiser\Model\ResourceModel\ThemeCustomer\CollectionFactory as CollectionFactory;
use TiDesign\Fundraiser\Model\ThemeCustomer\OptionSource\Status;

class GetThemeByCurrentCustomerId
{
    /**
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        protected CollectionFactory $collectionFactory,
        protected \Magento\Customer\Model\Session $customerSession
    ) {
    }

    /**
     * @param string $themeId
     * @return \TiDesign\Fundraiser\Model\ThemeCustomer
     */
    public function execute($themeId)
    {
        $customerId = $this->customerSession->getCustomerId();
        /** @var \TiDesign\Fundraiser\Model\ResourceModel\ThemeCustomer\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('theme_id', $themeId);
        $collection->addFieldToFilter('customer_id', $customerId);
        /** @var \TiDesign\Fundraiser\Model\ThemeCustomer $model */
        $model = $collection->getFirstItem();
        if (!$model->getId()) {
            $model->setThemeId($themeId);
            $model->setCustomerId($customerId);
            $model->setStatus(Status::PENDING_APPROVAL);
        }
        return $model;
    }
}
