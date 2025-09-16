<?php

namespace TiDesign\Fundraiser\Model\ResourceModel\Catalog\Category;

use TiDesign\Fundraiser\Model\Catalog\Category\CategoryIdsToArrayProcessor;
use TiDesign\Fundraiser\Model\ResourceModel\Theme as ThemeResourceModel;
use TiDesign\Fundraiser\Model\ResourceModel\ThemeCustomer as ThemeCustomerResourceModel;

class GetFundraiserCategoryIds
{
    /**
     * @param ThemeResourceModel $themeResourceModel
     * @param ThemeCustomerResourceModel $themeCustomerResourceModel
     * @param CategoryIdsToArrayProcessor $categoryIdsToArrayProcessor
     */
    public function __construct(
        protected ThemeResourceModel $themeResourceModel,
        protected ThemeCustomerResourceModel $themeCustomerResourceModel,
        protected CategoryIdsToArrayProcessor $categoryIdsToArrayProcessor
    ) {
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function execute()
    {
        $select = $this->getQuerySelect();
        $connection = $this->themeResourceModel->getConnection();
        $uniqueValue = [];
        foreach ($connection->fetchCol($select) as $categoryIds) {
            $categoryIds = $this->categoryIdsToArrayProcessor->execute($categoryIds);
            foreach ($categoryIds as $categoryId) {
                $uniqueValue[$categoryId] = $uniqueValue;
            }
        }
        return $uniqueValue;
    }

    /**
     * @return \Magento\Framework\DB\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    private function getQuerySelect()
    {
        $connection = $this->themeResourceModel->getConnection();
        return $connection->select()->from(
            ['theme_customer' => $this->themeCustomerResourceModel->getMainTable()],
            [new \Zend_Db_Expr('theme_customer.category_id')]
        );
    }
}
