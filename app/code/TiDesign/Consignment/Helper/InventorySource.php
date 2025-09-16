<?php
namespace TiDesign\Consignment\Helper;

use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\Data\SourceInterfaceFactory;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryAdminUi\Model\Source\SourceHydrator as InventorySourceHydrator;
use Magento\InventoryApi\Api\Data\StockSourceLinkInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
//use Magento\InventorySales\Model\GetAssignedStockIdForWebsiteInterface;
use Magento\InventorySalesApi\Model\GetAssignedStockIdForWebsiteInterface;
//    Magento\InventorySales\Model\ResourceModel\GetAssignedStockIdForWebsite
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\GetStockSourceLinksInterface;
use Magento\InventoryApi\Api\Data\StockSourceLinkInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\InventoryApi\Api\StockSourceLinksSaveInterface;

use Magento\Framework\App\Config\ScopeConfigInterface;
class InventorySource extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var SourceInterfaceFactory
     */
    private $sourceInterfaceFactory;

    /**
     * @var SourceRepositoryInterface
     */
    private $sourceRepositoryInterface;

    /**
     * @var InventorySourceHydrator
     */
    private $inventorySourceHydrator;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var GetAssignedStockIdForWebsiteInterface
     */
    private $getAssignedStockIdForWebsite;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var GetStockSourceLinksInterface
     */
    private $getStockSourceLinks;

    /**
     * @var StockSourceLinkInterfaceFactory
     */
    private $stockSourceLink;

    /**
     * @var DataObjectHelper
     */
    private $dataObject;

    /**
     * @var StockSourceLinksSaveInterface
     */
    private $stockSourceLinksSave;

	protected  	$scopeConfig;			 
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param SourceInterfaceFactory                $sourceInterfaceFactory
     * @param SourceRepositoryInterface             $sourceRepositoryInterface
     * @param InventorySourceHydrator               $inventorySourceHydrator
     * @param ManagerInterface                      $messageManager
     * @param \Magento\Framework\Event\Manager      $eventManager
     * @param StoreManagerInterface                 $storeManager
     * @param GetAssignedStockIdForWebsiteInterface $getAssignedStockIdForWebsite
     * @param SearchCriteriaBuilder                 $searchCriteriaBuilder
     * @param GetStockSourceLinksInterface          $getStockSourceLinks
     * @param StockSourceLinkInterfaceFactory       $stockSourceLink
     * @param DataObjectHelper                      $dataObject
     * @param StockSourceLinksSaveInterface         $stockSourceLinksSave
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        SourceInterfaceFactory $sourceInterfaceFactory,
        SourceRepositoryInterface $sourceRepositoryInterface,
        InventorySourceHydrator $inventorySourceHydrator,
        ManagerInterface $messageManager,
        StoreManagerInterface $storeManager,
        GetAssignedStockIdForWebsiteInterface $getAssignedStockIdForWebsite,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        GetStockSourceLinksInterface $getStockSourceLinks,
        StockSourceLinkInterfaceFactory $stockSourceLink,
        DataObjectHelper $dataObject,
		ScopeConfigInterface $scopeConfig,						  
        StockSourceLinksSaveInterface $stockSourceLinksSave
    ) {
        $this->_request = $context->getRequest();
        $this->sourceInterfaceFactory = $sourceInterfaceFactory;
        $this->sourceRepositoryInterface = $sourceRepositoryInterface;
        $this->inventorySourceHydrator = $inventorySourceHydrator;
        $this->messageManager = $messageManager;
        $this->storeManager = $storeManager;
        $this->getAssignedStockIdForWebsite = $getAssignedStockIdForWebsite;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->getStockSourceLinks = $getStockSourceLinks;
        $this->stockSourceLink = $stockSourceLink;
        $this->dataObject = $dataObject;
        $this->stockSourceLinksSave = $stockSourceLinksSave;
		$this->scopeConfig 					= $scopeConfig;							   
        parent::__construct($context);
    }

	public function addNew($data){
        $sourceCode 	= null;
		$newSourceData 	= [];
        $request 		= $this->_request;
		
        $sourceData 					= [];
        $sourceData['id_field_name'] 	= 'source_code';
        $sourceData['source_code'] 		= $data["source_code"];
        $sourceData['name'] 			= $data["source_name"];
        $sourceData['email'] 			= '';
        $sourceData['contact_name'] 	= '';
        $sourceData['enabled'] 			= 1;
        $sourceData['description'] 		= '';
        $sourceData['latitude'] 		= '';
        $sourceData['longitude'] 		= '';
        $sourceData['country_id'] 		= $data["source_city"];
        $sourceData['region_id'] 		= '';
        $sourceData['city'] 			= '';
        $sourceData['postcode'] 		= $data["source_postcode"];
        $sourceData['use_default_carrier_config'] = 1;
        $sourceData['carrier_codes'] 	= '';
        $sourceData['disable_source_code'] = true;
        $sourceData['phone'] 			= '';
        $sourceData['fax'] 				= '';
        $sourceData['region'] 			= '';
        $sourceData['street'] 			= '';
		
        $request->setPostValue('general', $sourceData);
        $request->setPostValue('form_key', 'or1eYs7K4PBsLbO1');
		
		$requestData = $request->getPost()->toArray();
		$sourceCodeQueryParam = $request->getQuery(SourceInterface::SOURCE_CODE);
		
		try {
            $inventorySource = (null !== $sourceCodeQueryParam)
                ? $this->sourceRepositoryInterface->get($sourceCodeQueryParam)
                : $this->sourceInterfaceFactory->create();

            $inventorySource = $this->inventorySourceHydrator->hydrate($inventorySource, $requestData);

            $this->_eventManager->dispatch(
                'controller_action_inventory_populate_source_with_data',
                [
                    'request' => $request,
                    'source' => $inventorySource,
                ]
            );

            $this->sourceRepositoryInterface->save($inventorySource);

            $this->_eventManager->dispatch(
                'controller_action_inventory_source_save_after',
                [
                    'request' => $request,
                    'source' => $inventorySource,
                ]
            );
            $sourceCode 	= $requestData['general']['source_code'];
            $newSourceData['source_code'] = $sourceCode;
            $newSourceData['name'] = $requestData['general']['name'];
            $newSourceData['position'] = 1;
            $newSourceData['record_id'] = $sourceCode;
            $newSourceData['priority'] = 1;
			$this->assignSourceToStocks($sourceCode, $newSourceData);	

            $this->messageManager->addSuccessMessage(__('The Source has been saved.'));
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $sourceCode = 0;
            $this->messageManager->addErrorMessage(__('The Source does not exist.'));
        } catch (\Magento\Framework\Validation\ValidationException $e) {
            $sourceCode = 0;
            foreach ($e->getErrors() as $localizedError) {
                $this->messageManager->addErrorMessage($localizedError->getMessage());
            }
        } catch (\Magento\Framework\Exception\CouldNotSaveException $e) {
            $sourceCode = 0;
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $sourceCode = 0;
            $this->messageManager->addErrorMessage(__('Could not save Source.'));
        }
	}
	
    public function prepareSourceRequestData()
    {
        $sourceCode = null;
        $request = $this->_request;
        $sourceData = [];
        $sourceData['id_field_name'] = 'source_code';
        $sourceData['source_code'] = 'us-florida';
        $sourceData['name'] = 'US Florida';
        $sourceData['email'] = '';
        $sourceData['contact_name'] = '';
        $sourceData['enabled'] = 1;
        $sourceData['description'] = '';
        $sourceData['latitude'] = '';
        $sourceData['longitude'] = '';
        $sourceData['country_id'] = 'US';
        $sourceData['region_id'] = 12;
        $sourceData['city'] = '';
        $sourceData['postcode'] = '95004';
        $sourceData['use_default_carrier_config'] = 1;
        $sourceData['carrier_codes'] = '';
        $sourceData['disable_source_code'] = true;
        $sourceData['phone'] = '';
        $sourceData['fax'] = '';
        $sourceData['region'] = '';
        $sourceData['street'] = '';
        $request->setPostValue('general', $sourceData);
        $request->setPostValue('form_key', 'or1eYs7K4PBsLbO1');
    }

    public function processSource()
    {
        // prepare source request data
        $this->prepareSourceRequestData();
        $sourceCode = null;
        $newSourceData = [];
        $request = $this->_request;
        $requestData = $request->getPost()->toArray();
        $sourceCodeQueryParam = $request->getQuery(SourceInterface::SOURCE_CODE);
        try {
            $inventorySource = (null !== $sourceCodeQueryParam)
                ? $this->sourceRepositoryInterface->get($sourceCodeQueryParam)
                : $this->sourceInterfaceFactory->create();

            $inventorySource = $this->inventorySourceHydrator->hydrate($inventorySource, $requestData);

            $this->_eventManager->dispatch(
                'controller_action_inventory_populate_source_with_data',
                [
                    'request' => $request,
                    'source' => $inventorySource,
                ]
            );

            $this->sourceRepositoryInterface->save($inventorySource);

            $this->_eventManager->dispatch(
                'controller_action_inventory_source_save_after',
                [
                    'request' => $request,
                    'source' => $inventorySource,
                ]
            );
            $sourceCode = $requestData['general']['source_code'];
            $newSourceData['source_code'] = $sourceCode;
            $newSourceData['name'] = $requestData['general']['name'];
            $newSourceData['position'] = 1;
            $newSourceData['record_id'] = $sourceCode;
            $newSourceData['priority'] = 1;

            $this->messageManager->addSuccessMessage(__('The Source has been saved.'));
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $sourceCode = 0;
            $this->messageManager->addErrorMessage(__('The Source does not exist.'));
        } catch (\Magento\Framework\Validation\ValidationException $e) {
            $sourceCode = 0;
            foreach ($e->getErrors() as $localizedError) {
                $this->messageManager->addErrorMessage($localizedError->getMessage());
            }
        } catch (\Magento\Framework\Exception\CouldNotSaveException $e) {
            $sourceCode = 0;
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $sourceCode = 0;
            $this->messageManager->addErrorMessage(__('Could not save Source.'));
        }

        $this->assignSourceToStocks($sourceCode, $newSourceData);
    }

    public function assignSourceToStocks($sourceCode, $newSourceData)
    {
        try {
            if ($sourceCode) {
//                $allWebsites = $this->storeManager->getWebsites();
//               foreach ($allWebsites as $website) {
//                    $websiteCode = $website->getCode();
//                    $stockId = $this->getAssignedStockIdForWebsite->execute($websiteCode);
					$stockId = $this->getConfig("consignment/consignment_status/default_stock");
                    $searchCriteria = $this->searchCriteriaBuilder->addFilter(
                        StockSourceLinkInterface::STOCK_ID,
                        $stockId
                    )->create();

                    $result = [];
                    foreach ($this->getStockSourceLinks->execute($searchCriteria)->getItems() as $link) {
                        $result[$link->getSourceCode()] = $link;
                    }

                    if (isset($result[$sourceCode])) {
                        $link = $result[$sourceCode];
                    } else {
                        /** @var StockSourceLinkInterface $link */
                        $link = $this->stockSourceLink->create();
                    }

                    $newSourceData[StockSourceLinkInterface::STOCK_ID] = $stockId;
                    $this->dataObject->populateWithArray($link, $newSourceData, StockSourceLinkInterface::class);

                    $result[] = $link;

                    if (!empty($result)) {
                        $this->stockSourceLinksSave->execute($result);
                    }
//                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Could not save Source to Stock.'));
        }
    }
	public function checkSource($code){
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('source_code', $code)->create();
        $sourceInfo = null;
        try {
            $sourceData = $this->sourceRepositoryInterface->getList($searchCriteria);
            if ($sourceData->getTotalCount()) {
                $sourceInfo = $sourceData->getItems();
            }
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $sourceInfo;
	}
	public function getConfig($config_path)
	{
		return $this->scopeConfig->getValue(
			$config_path,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}
}