<?php
namespace TiDesign\Consignment\Controller\Adminhtml\Consignmentlist;


use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use TiDesign\Consignment\Model\Consignmentlist;

class Save extends \Magento\Backend\App\Action
{
	private 	$scopeConfig;
	protected 	$categoryFactory;	
	protected 	$sourceHelper;	
	protected 	$mailHelper;	
	protected 	$storeManager;
	protected 	$customerFactory;
	protected 	$dataPersistor;
	private 	$consignmentlistFactory;
	private 	$consignmentlistRepository;
	
	public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
		\TiDesign\Consignment\Model\ConsignmentlistFactory $consignmentlistFactory = null,
        \TiDesign\Consignment\Api\ConsignmentlistRepositoryInterface $consignmentlistRepository = null,
		ScopeConfigInterface $scopeConfig,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\TiDesign\Consignment\Helper\InventorySource $sourceHelper,
		\TiDesign\Consignment\Helper\Mailer $mailHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->dataPersistor = $dataPersistor;
		$this->consignmentlistFactory = $consignmentlistFactory
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\TiDesign\Consignment\Model\ConsignmentlistFactory::class);
        $this->consignmentlistRepository = $consignmentlistRepository
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\TiDesign\Consignment\Api\ConsignmentlistRepositoryInterface::class);
        parent::__construct($context);
		$this->scopeConfig 					= $scopeConfig;
		$this->categoryFactory 				= $categoryFactory;
		$this->sourceHelper 				= $sourceHelper;
		$this->mailHelper 					= $mailHelper;
		$this->storeManager     			= $storeManager;
        $this->customerFactory  			= $customerFactory;
    }
	public function execute()
    {
		$resultRedirect = $this->resultRedirectFactory->create();
		$postData = $this->getRequest()->getPostValue();
		if (!empty($postData)) {


			$data['td_name'] 		= $postData['td_name'];
			$data['td_website'] 	= $postData['td_website'];
			$data['td_status'] 		= (array_key_exists ('td_status', $postData)) ? $postData['td_status'] :  0 ;
						
				//category			
			if($postData["category_type_selector"] == "category_add_option"){
				$categoryId		= $this->getWebsiteConfig("consignment/consignment_status/container_category_id", $data['td_website']);
				$parentCategory = $this->categoryFactory->create()->load($categoryId);
				$catFactory 	= $this->categoryFactory->create();
				$catFactory->setPath($parentCategory->getPath())
								->setParentId($categoryId)
								->setName($postData["new_category"])
								->setIsActive(true)
								->setIncludeInMenu(false);
				$catFactory->save();
				$data["td_category"] = $catFactory->getId();
			}else{
				$data['td_category'] 	= $postData['td_category'];
			}

			/* source */
			if($postData["source_type_selector"] == "source_add_option"){
				$this->sourceHelper->addNew($postData);
				$data['td_source'] 		= $postData['source_code'];
			}else{
				$data['td_source'] 		= $postData['td_source'];
			}

    

			if($postData["customer_type_selector"] == "customer_add_option"){
				$group		= $this->getConfig("consignment/consignment_status/customer_group");
				//$websiteId  = $this->storeManager->getWebsite()->getWebsiteId();
				//$storeId	= $this->storeManager->getWebsite($data['td_website'])->getStores();
				$storeName	= $this->storeManager->getWebsite($data['td_website'])->getDefaultStore()->getName();
				$customer   = $this->customerFactory->create();
				//$customer->setWebsiteId($websiteId);
				$customer->setWebsiteId($data['td_website']);
				$customer->setCreatedIn($storeName);
				$customer->setForceConfirmed(true);
				$customer->setEmail($postData["new_customer_email"]); 
				$customer->setFirstname($postData["new_customer_name"]);
				$customer->setLastname($postData["new_customer_surname"]);
				$customer->setPassword($postData["new_customer_password"]);
				$customer->setGroupId($group);			
				$customer->save();
				if (!empty($postData['send_password_mail'])) {
					if($postData["send_password_mail"] == "on"){
						$this->mailHelper->sendMail($postData);
					}
				}
				$data['td_customer'] = $customer->getId();
			}else{
				$data['td_customer'] = $postData["customer-selector"];
			}			
			//echo "<pre>";print_r($data);
			//die("save");
			if (empty($postData['td_id'])) {
                $data['td_id'] = null;
            }else{
				$data['td_id'] = $postData['td_id'];
			}
			
			$id = $postData['td_id'];
//			echo "id:" . $id ."<br>";
            if ($id) {
//				echo "-1";
                try {
                    $model = $this->consignmentlistRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This record no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }else{
//				echo "-2";
				$model = $this->consignmentlistFactory->create();
			}
			$model->setData($data);
			//echo "<pre>";print_r($data);
			//die("save");
            try {
                $this->consignmentlistRepository->save($model);
                $this->messageManager->addSuccessMessage(__('Save successfull...'));
                $this->dataPersistor->clear('consignment_consignmentlist');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getTdId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?:$e);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the news.'));
            }	
            $this->dataPersistor->set('consignment_consignmentlist', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('td_id')]);
			
		}
		return $resultRedirect->setPath('*/*/');
	}
	
	public function getConfig($config_path)
	{
		return $this->scopeConfig->getValue(
			$config_path,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}	
	public function getWebsiteConfig($config_path, $wesiteId)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES,
			$wesiteId
        );
    }
}