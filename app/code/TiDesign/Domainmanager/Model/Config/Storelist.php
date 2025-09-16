<?php

namespace TiDesign\Domainmanager\Model\Config;

use Magento\Framework\Option\ArrayInterface;
use Magento\Store\Model\StoreManagerInterface;

class Storelist implements ArrayInterface
{
    protected $_storeManager;

    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
    }
    
    public function toOptionArray($addEmpty = true)
    {
        $storeCollection = $this->_storeManager->getStores($withDefault = false);

        $options = [];


        foreach ($storeCollection as $store) {
            $options[] = ['label' => $store->getName(), 'value' => $store->getCode()];
        }

        return $options;
    }
}