<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\Config\ResourceModel\Config;

/**
 * Configuration data resource
 */
class Data extends \Magento\Config\Model\ResourceModel\Config\Data
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ecombricks_inventory__source_core_config_data', 'config_id');
    }

    /**
     * Check unique
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _checkUnique(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from($this->getMainTable(), [$this->getIdFieldName()]);
        $select->where('scope = :scope');
        $select->where('scope_id = :scope_id');
        $select->where('path = :path');
        $select->where('source_code = :source_code');
        $bind = [
            'scope' => $object->getScope(),
            'scope_id' => $object->getScopeId(),
            'path' => $object->getPath(),
            'source_code' => $object->getSourceCode(),
        ];
        $configId = $connection->fetchOne($select, $bind);
        if ($configId) {
            $object->setId($configId);
        }
        return $this;
    }
}