<?php

namespace CityBeach\Integration\Model;

use \Magento\Framework\App\ProductMetadataInterface;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\Module\ModuleListInterface;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \CityBeach\Integration\Api\CityBeachInterface;
use \Psr\Log\LoggerInterface;

class CityBeach implements CityBeachInterface
{
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /*
     * @var \Magento\Framework\ObjectManagerInterface $objectManager
     */
    private $objectManager;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ScopeConfigInterface
     */
    private $scope;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scope
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ProductMetadataInterface $productMetadata,
        ModuleListInterface $moduleList,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ScopeConfigInterface $scope,
        LoggerInterface $logger)
    {
        $this->objectManager = $objectManager;
        $this->productMetadata = $productMetadata;
        $this->moduleList = $moduleList;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->scope = $scope;
        $this->logger = $logger;
    }

    /**
     * Query magento for system and debugging information.
     *
     * @api
     * @return array
     */
    public function execute() {
      $php_version = phpversion();
      $magento_version = $this->productMetadata->getVersion();
      $plugin_versions = array();

      $modules_metadatas = $this->moduleList->getAll();
      foreach ($modules_metadatas as $module_name => $modules_metadata)
      {
        $module_name_lowercase = strtolower($module_name);
        if (strpos($module_name_lowercase, 'citybeach') === 0)
        {
          $plugin_versions[$module_name] = $modules_metadata;
        }
      }

      $paymentMethodList = $this->scope->getValue('payment');

      return [[
        'php_version' => $php_version,
        'magento_version' => $magento_version,
        'plugins' => $plugin_versions,
        'payment_methods' => $paymentMethodList,
      ]];
    }

    /**
     * Query magento for system available inventory sources.
     *
     * @api
     * @return mixed
     */
    public function getSourceDetails() {
        /* @var $moduleManager \Magento\Framework\Module\Manager */
        $moduleManager = $this->objectManager->create('\Magento\Framework\Module\Manager');

        if ($moduleManager->isEnabled('Magento_InventorySalesApi')) {
            $sourceRepository = $this->objectManager->create('Magento\InventoryApi\Api\SourceRepositoryInterface');
            $searchCriteria = $this->searchCriteriaBuilder->create();
            try {
                $sourceData = $sourceRepository->getList($searchCriteria);
                if ($sourceData->getTotalCount()) {
                    return [[
                        'sources' => $sourceData->getItems()
                    ]];
                }
                else {
                    return [[
                        'sources' => []
                    ]];
                }
            } catch (Exception $exception) {
                $this->logger->error($exception->getMessage());
                return [[
                    'error_message' => $exception->getMessage(),
                    'error_detail' => $exception->__toString()
                ]];
            }
        }

        return [];
    }

}
