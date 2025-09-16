<?php
declare(strict_types=1);

namespace Amasty\Pgrid\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\InventoryLowQuantityNotificationApi\Api\Data\SourceItemConfigurationInterface;

class SourceItemResource
{
    public const INVENTORY_ITEM_TABLE = 'inventory_source_item';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var AdapterInterface
     */
    private $connection;

    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->connection = $resourceConnection->getConnection();
    }

    public function getSourceItemData($productSku, $sourceCode)
    {
        $inventoryItemTable = $this->resourceConnection->getTableName(self::INVENTORY_ITEM_TABLE);

        $select = $this->connection->select()
            ->from($inventoryItemTable)
            ->where(SourceItemConfigurationInterface::SOURCE_CODE . ' = ?', $sourceCode)
            ->where(SourceItemConfigurationInterface::SKU . ' = ?', $productSku);

        return $this->connection->fetchRow($select);
    }
}
