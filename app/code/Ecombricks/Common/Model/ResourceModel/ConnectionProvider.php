<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\Common\Model\ResourceModel;

/**
 * Resource connection provider
 */
class ConnectionProvider
{
    /**
     * Resource connection
     * 
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * Expression factory
     * 
     * @var \Magento\Framework\DB\Sql\ExpressionFactory
     */
    private $expressionFactory;

    /**
     * Resource name
     * 
     * @var string
     */
    private $resourceName;

    /**
     * Connection
     * 
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * Constructor
     * 
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Framework\DB\Sql\ExpressionFactory $expressionFactory
     * @param string $resourceName
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\DB\Sql\ExpressionFactory $expressionFactory,
        string $resourceName = \Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION
    )
    {
        $this->resourceConnection = $resourceConnection;
        $this->expressionFactory = $expressionFactory;
        $this->resourceName = $resourceName;
    }
    
    /**
     * Get connection
     * 
     * @param string|null $resourceName
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    public function getConnection(string $resourceName = null): \Magento\Framework\DB\Adapter\AdapterInterface
    {
        return $this->resourceConnection->getConnection($resourceName ?: $this->resourceName);
    }

    /**
     * Get select
     * 
     * @return \Magento\Framework\DB\Select
     */
    public function getSelect(): \Magento\Framework\DB\Select
    {
        return $this->getConnection()->select();
    }
    
    /**
     * Get table
     * 
     * @param string $tableName
     * @param string|null $resourceName
     * @return string
     */
    public function getTable(string $tableName, string $resourceName = null): string
    {
        return $this->resourceConnection->getTableName($tableName, $resourceName ?: $this->resourceName);
    }

    /**
     * Get condition
     * 
     * @param array $subConditions
     * @param string $joinOperator
     * @return string
     */
    public function getCondition(array $subConditions, string $joinOperator = 'AND'): string
    {
        return '('.implode(') '.$joinOperator.' (', $subConditions).')';
    }

    /**
     * Get SQL
     * 
     * @param string $expression
     * @return \Magento\Framework\DB\Sql\Expression
     */
    public function getSql(string $expression): \Magento\Framework\DB\Sql\Expression
    {
        return $this->expressionFactory->create(['expression' => $expression]);
    }

    /**
     * Get check SQL
     * 
     * @param string $expression
     * @param string $true
     * @param string $false
     * @return \Magento\Framework\DB\Sql\Expression
     */
    public function getCheckSql(string $expression, string $true, string $false): \Magento\Framework\DB\Sql\Expression
    {
        return $this->getSql((string) $this->getConnection()->getCheckSql($expression, $true, $false));
    }

    /**
     * Get if NULL SQL
     * 
     * @param string $expression
     * @param string $value
     * @return \Magento\Framework\DB\Sql\Expression
     */
    public function getIfNullSql(string $expression, string $value = null): \Magento\Framework\DB\Sql\Expression
    {
        return $this->getSql((string) $this->getConnection()->getIfNullSql($expression, $value));
    }

    /**
     * Get round SQL
     * 
     * @param string $value
     * @return \Magento\Framework\DB\Sql\Expression
     */
    public function getRoundSql(string $value): \Magento\Framework\DB\Sql\Expression
    {
        return $this->getSql(sprintf("ROUND(%s, 4)", $value));
    }

    /**
     * Get percent value SQL
     * 
     * @param string $value
     * @param string $percentage
     * @return \Magento\Framework\DB\Sql\Expression
     */
    public function getPercentValueSql(string $value, string $percentage): \Magento\Framework\DB\Sql\Expression
    {
        return $this->getSql(sprintf("%s * (%s / 100)", $value, $percentage));
    }

    /**
     * Get inverse percent value SQL
     * 
     * @param string $value
     * @param string $percentage
     * @return \Magento\Framework\DB\Sql\Expression
     */
    public function getInversePercentValueSql(string $value, string $percentage): \Magento\Framework\DB\Sql\Expression
    {
        return $this->getSql(sprintf("%s * (1 - %s / 100)", $value, $percentage));
    }

    /**
     * Get percent value round SQL
     * 
     * @param string $value
     * @param string $percentage
     * @return \Magento\Framework\DB\Sql\Expression
     */
    public function getPercentValueRoundSql(string $value, string $percentage): \Magento\Framework\DB\Sql\Expression
    {
        return $this->getRoundSql((string) $this->getPercentValueSql($value, $percentage));
    }

    /**
     * Get inverse percent value round SQL
     * 
     * @param string $value
     * @param string $percentage
     * @return \Magento\Framework\DB\Sql\Expression
     */
    public function getInversePercentValueRoundSql(string $value, string $percentage): \Magento\Framework\DB\Sql\Expression
    {
        return $this->getRoundSql((string) $this->getInversePercentValueSql($value, $percentage));
    }
}