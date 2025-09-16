<?php
namespace TiDesign\Consignment\Model\Config;

use Exception;
class Customergroup implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(
		\Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory
	){
        $this->_groupCollectionFactory = $groupCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
		$options = [];
        try {
            $options = $this->_groupCollectionFactory->create()->loadData()->toOptionArray();
		}catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
		
        return $options;
    }
}