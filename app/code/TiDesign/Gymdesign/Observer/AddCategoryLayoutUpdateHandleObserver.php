<?php
declare(strict_types=1);
namespace TiDesign\Gymdesign\Observer;

use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Layout as Layout;
use Magento\Framework\View\Layout\ProcessorInterface as LayoutProcessor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Model\CategoryFactory;
/**
 *  AddCategoryLayoutUpdateHandleObserver
 */
class AddCategoryLayoutUpdateHandleObserver implements ObserverInterface
{
    /**
     * Category Custom Layout Name
     *
     * It's the filename of layout phisically located
     * at `[Vendor]/[ModuleName]/view/frontend/layout/catalog_category_view_custom_layout.xml`
     */
    const LAYOUT_HANDLE_NAME = 'catalog_category_view_custom_layout';
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @param Registry $registry
     */
	 
	protected  	$scopeConfig;
	public 	$categoryFactory;
	 
	 
    public function __construct(
		Registry $registry,
		ScopeConfigInterface $scopeConfig,
		CategoryFactory $categoryFactory
	){
        $this->registry 			= $registry;
		$this->scopeConfig 			= $scopeConfig;
		$this->categoryFactory 		= $categoryFactory; 
    }
    /**
     * @param EventObserver $observer
     *
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        /** @var Event $event */
        $event = $observer->getEvent();
        $actionName = $event->getData('full_action_name');
        /** @var CategoryModel|null $category **/
        $category 	= $this->registry->registry('current_category');
		if($category){
			
			$categoryId	= $category->getId();
			$container 	= $this->getConfig("gymdesign/gymdesign_setings/container_category_id");
			$stat		= $this->compareCategory($categoryId, $container);
			if (
				$category &&
				$actionName === 'catalog_category_view' &&
				$stat
			) {
				/** @var Layout $layout */
				$layout = $event->getData('layout');
				/** @var LayoutProcessor $layoutUpdate */
				$layoutUpdate = $layout->getUpdate();
				// check if Category Display Mode is "Mixed"
//				if ($category->getData('display_mode') === CategoryModel::DM_MIXED) {
					$layoutUpdate->addHandle(static::LAYOUT_HANDLE_NAME);
//				}
			}
		}
    }
	public function getConfig($config_path)
	{
		return $this->scopeConfig->getValue(
			$config_path,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}
	private function compareCategory($category, $container){
		$cc = $this->categoryFactory->create()->load($category); 
		
		if(
			($category == $container) ||
			($cc->getParentCategory()->getId() == $container)
		){
			return true;
		}
		return false;
		
	}
}