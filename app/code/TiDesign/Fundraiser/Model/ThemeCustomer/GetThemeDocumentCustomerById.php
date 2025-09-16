<?php

namespace TiDesign\Fundraiser\Model\ThemeCustomer;

use TiDesign\Fundraiser\Model\ResourceModel\ThemeCustomer\Grid\CollectionFactory as GridCollectionFactory;

class GetThemeDocumentCustomerById
{
    /**
     * @param GridCollectionFactory $gridCollectionFactory
     */
    public function __construct(
        protected GridCollectionFactory $gridCollectionFactory
    ) {
    }

    /**
     * @param int $id
     * @return \Magento\Framework\View\Element\UiComponent\DataProvider\Document
     */
    public function execute($id)
    {
        $gridCollection = $this->gridCollectionFactory->create();
        $gridCollection->addFieldToFilter('id', $id);
        return $gridCollection->getFirstItem();
    }
}
