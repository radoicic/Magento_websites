<?php
namespace TiDesign\Domainmanager\Model\Config\Attribute;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Option\ArrayInterface;
use Magento\Store\Model\StoreManagerInterface;

class Storelist extends AbstractSource
{
    protected $_storeManager;
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
    }
	
	public function getAllOptions() 
	{
        $storeCollection = $this->_storeManager->getStores($withDefault = false);

        $options = [];
		$options[] = ['label' => "No Store Selected", 'value' => 0];

        foreach ($storeCollection as $store) {
            $options[] = ['label' => $store->getName(), 'value' => $store->getId()];
        }

        return $options;
    }
}