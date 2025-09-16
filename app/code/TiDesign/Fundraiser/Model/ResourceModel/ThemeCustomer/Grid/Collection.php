<?php

namespace TiDesign\Fundraiser\Model\ResourceModel\ThemeCustomer\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\DB\Sql\ConcatExpressionFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Psr\Log\LoggerInterface as Logger;

class Collection extends SearchResult
{
    private $resourceConnection;

    /**
     * {@inheritdoc}
     */
    protected $document = Document::class;

    /**
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param ConcatExpressionFactory $concatExpressionFactory
     * @param string $mainTable
     * @param string $resourceModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        protected ConcatExpressionFactory $concatExpressionFactory,
        $mainTable = 'fundraiser_theme_customer',
        $resourceModel = \TiDesign\Fundraiser\Model\ResourceModel\ThemeCustomer::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    public function _initSelect()
    {
        parent::_initSelect();
        $customerNameExpression = $this->getCustomerNameExpression();
        $this->getSelect()->joinLeft(
            ['theme' => 'fundraiser_theme'],
            'main_table.theme_id = theme.theme_id',
            ['theme_name' => 'theme.theme_name']
        )->joinInner(
            ['customer' => $this->_resource->getTable('customer_entity')],
            'main_table.customer_id = customer.entity_id',
            ['customer_email' => 'customer.email', 'customer_name' => $customerNameExpression]
        );
        $this->addFilterToMap('id', 'main_table.id');
        $this->addFilterToMap('theme_name', 'theme.theme_name');
        $this->addFilterToMap('customer_email', 'customer.email');
        $this->addFilterToMap('customer_name', $customerNameExpression);
    }

    /**
     * @return string
     */
    private function getCustomerNameExpression()
    {
        $expression = $this->concatExpressionFactory->create([
            'resource' => $this->resourceConnection,
            'columns' => [
                ['tableAlias' => 'customer', 'columnName' => 'prefix'],
                ['tableAlias' => 'customer', 'columnName' => 'firstname'],
                ['tableAlias' => 'customer', 'columnName' => 'middlename'],
                ['tableAlias' => 'customer', 'columnName' => 'lastname'],
                ['tableAlias' => 'customer', 'columnName' => 'suffix'],
            ]
        ]);
        return $expression->__toString();
    }

    /**
     * @param $field
     * @param $condition
     * @return Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field !== 'category_id' || !is_array($condition) || !isset($condition['in'])) {
            return parent::addFieldToFilter($field, $condition);
        }
        $categoryIds = $condition['in'];
        $field = "main_table.$field";
        $condition = array_map(fn($catId) => new \Zend_Db_Expr("FIND_IN_SET($catId, $field)"), $categoryIds);
        $condition = implode(' OR ', $condition);
        $this->getSelect()->where(new \Zend_Db_Expr("($condition)"));
        return $this;
    }
}
