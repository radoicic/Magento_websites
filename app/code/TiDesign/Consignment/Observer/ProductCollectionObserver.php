<?php
namespace TiDesign\Consignment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * ProductCollectionObserver
 */
class ProductCollectionObserver implements ObserverInterface
{

	protected $_registry;
    protected $consignmentHelper;

	public function __construct(
		\Magento\Framework\Registry $registry,
		\TiDesign\Consignment\Helper\Consignment $consignmentHelper
	){
		$this->_registry = $registry;
		$this->consignmentHelper = $consignmentHelper;
	}
    /**
     * Handler for load product collection event
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
		//echo "aaa";die();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$request = $objectManager->get('\Magento\Framework\App\Request\Http');
		if ($request->getFullActionName() == 'catalog_category_view') {
			$isConsCat = ($this->isConsCat() ? true : false);
		}else{
			$isConsCat = false;
		}

		if($isConsCat == true){
			$categoryId = $this->getCategoryId();
			$cons = $this->consignmentHelper->getConsignmentFromCategory($categoryId);
			if($cons->getSize()){

				$cs = $cons->getData();
				$disabledProducts = explode(',',$cs[0]["td_disabled"] ?? "");
				$observer->getEvent()
					->getCollection()
					->addAttributeToFilter('entity_id',['nin'=>$disabledProducts]);
			}
		}

       return $this;
    }

	private function isConsCat()
	{
		$categoryId = $this->getCategoryId();
		$concat =  $this->consignmentHelper->isConsignmentCategory($categoryId);
		return $concat;
	}
	private function getCategoryId()
	{
		return $this->_registry->registry('current_category')->getId();
	}
}
