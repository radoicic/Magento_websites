<?php
namespace TiDesign\Consignment\Model\Filters\Category;

use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class CategoryList implements ArrayInterface
{
    protected $_categoryCollectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->_categoryCollectionFactory = $collectionFactory;
    }
    
    public function toOptionArray($addEmpty = true)
    {
        $categoryCollection = $this->_categoryCollectionFactory->create()->addAttributeToSelect('name')->setOrder('name', 'ASC');
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$conshelper = $objectManager->create('TiDesign\Consignment\Helper\Consignment');
		$concat	= array_column($conshelper->getConsignmentCategories(null,true)->toArray(),'entity_id');
		
        $options = [];

        if ($addEmpty) {
			$stext =(string)  __('Select Category');
			$options[] = ['label' => $stext , 'value' => ''];
        }

        foreach ($categoryCollection as $category) {
			if (in_array($category->getId(), $concat)) {
				$options[] = ['label' => $category->getName() , 'value' => $category->getId()];
			}
        }

        return $options;
    }
}

