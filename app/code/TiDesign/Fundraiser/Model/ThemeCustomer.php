<?php

namespace TiDesign\Fundraiser\Model;

use Magento\Framework\Model\AbstractModel;
use TiDesign\Fundraiser\Model\Catalog\Category\CategoryIdsToArrayProcessor;
use TiDesign\Fundraiser\Model\ResourceModel\ThemeCustomer as ResourceModel;

class ThemeCustomer extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'fundraiser_theme_customer_model';

    /**
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        protected CategoryIdsToArrayProcessor $categoryIdsToArrayProcessor,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize magento model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @return int[]
     */
    public function getCategoryIds()
    {
        $categoryIds = $this->getData('category_id') ?: '';
        return $this->categoryIdsToArrayProcessor->execute($categoryIds);
    }
}
