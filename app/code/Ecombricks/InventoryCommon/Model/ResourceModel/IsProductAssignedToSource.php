<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model\ResourceModel;

/**
 * Is product assigned to source
 */
class IsProductAssignedToSource
{
    /**
     * $connectionProvider
     * 
     * @var \Ecombricks\Common\Model\ResourceModel\ConnectionProvider
     */
    private $connectionProvider;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\Model\ResourceModel\ConnectionProvider $connectionProvider
    )
    {
        $this->connectionProvider = $connectionProvider;
    }

    /**
     * Execute
     * 
     * @param string $sku
     * @param string $sourceCode
     * @return bool
     */
    public function execute(string $sku, string $sourceCode): bool
    {
        $connection = $this->connectionProvider->getConnection();
        $select = $connection->select();
        $select->from(
            ['source_item' => $this->connectionProvider->getTable(\Magento\Inventory\Model\ResourceModel\SourceItem::TABLE_NAME_SOURCE_ITEM)]
        );
        $select->where(
            $this->connectionProvider->getCondition([
                $connection->quoteInto('source_item.'.\Magento\InventoryApi\Api\Data\SourceItemInterface::SKU.' = ?', $sku),
                $connection->quoteInto('source_item.'.\Magento\InventoryApi\Api\Data\SourceItemInterface::SOURCE_CODE.' = ?', $sourceCode),
            ], 'AND')
        );
        return (bool) $connection->fetchOne($select);
    }
}