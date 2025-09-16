<?php
namespace TiDesign\Consignment\Block\Adminhtml\System\Config\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Customergroups implements \Magento\Framework\Option\ArrayInterface
{
	protected $_options; 
	
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
        if (!$this->_options) {
            $this->_options = $this->_groupCollectionFactory->create()->loadData()->toOptionArray();
        }
        return $this->_options;
    }
}
