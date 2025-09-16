<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\App\Config\Config\Source;

/**
 * Runtime configuration source plugin
 */
class RuntimeConfigSource
{
    /**
     * Source collection factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Config\ResourceModel\Config\Data\CollectionFactory
     */
    private $sourceCollectionFactory;

    /**
     * Collection factory
     * 
     * @var \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory
     */
    private $collectionFactory;

    /**
     * Scope code resolver
     * 
     * @var \Magento\Framework\App\Config\ScopeCodeResolver
     */
    private $scopeCodeResolver;

    /**
     * Converter
     * 
     * @var \Magento\Framework\App\Config\Scope\Converter
     */
    private $converter;

    /**
     * Product metadata
     * 
     * @var \Magento\Framework\App\ProductMetadata 
     */
    private $productMetadata;

    /**
     * Deployment configuration
     * 
     * @var \Magento\Framework\App\DeploymentConfig 
     */
    private $deploymentConfig;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Config\ResourceModel\Config\Data\CollectionFactory $sourceCollectionFactory
     * @param \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\Config\ScopeCodeResolver $scopeCodeResolver
     * @param \Magento\Framework\App\Config\Scope\Converter $converter
     * @param \Magento\Framework\App\ProductMetadata $productMetadata
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\Config\ResourceModel\Config\Data\CollectionFactory $sourceCollectionFactory,
        \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Config\ScopeCodeResolver $scopeCodeResolver,
        \Magento\Framework\App\Config\Scope\Converter $converter,
        \Magento\Framework\App\ProductMetadata $productMetadata
    )
    {
        $this->sourceCollectionFactory = $sourceCollectionFactory;
        $this->collectionFactory = $collectionFactory;
        $this->scopeCodeResolver = $scopeCodeResolver;
        $this->converter = $converter;
        $this->productMetadata = $productMetadata;
        if (version_compare($this->productMetadata->getVersion(), '2.3.6', '>=')) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->deploymentConfig = $objectManager->get(\Magento\Framework\App\DeploymentConfig::class);
        }
    }
    
    /**
     * Around get
     *
     * @param \Magento\Config\App\Config\Source\RuntimeConfigSource $subject
     * @param \Closure $proceed
     * @param string $path
     * @return array
     */
    public function aroundGet(
        \Magento\Config\App\Config\Source\RuntimeConfigSource $subject,
        \Closure $proceed,
        $path
    )
    {
        if (version_compare($this->productMetadata->getVersion(), '2.3.6', '>=')) {
            $dataObject = new \Magento\Framework\DataObject($this->deploymentConfig->isDbAvailable() ? $this->loadConfig() : []);
        } else {
            $dataObject = new \Magento\Framework\DataObject($this->loadConfig());
        }
        return $dataObject->getData($path) !== null ? $dataObject->getData($path) : null;
    }
    
    /**
     * Get configuration values
     * 
     * @return \Magento\Framework\App\Config\ValueInterface[]
     */
    private function getConfigValues()
    {
        try {
            return $this->collectionFactory->create()->getItems();
        } catch (\DomainException $exception) {
            return [];
        } catch (\Magento\Framework\DB\Adapter\TableNotFoundException $exception) {
            return [];
        }
    }
    
    /**
     * Get source configuration values
     * 
     * @return \Magento\Framework\App\Config\ValueInterface[]
     */
    private function getSourceConfigValues()
    {
        try {
            return $this->sourceCollectionFactory->create()->getItems();
        } catch (\DomainException $exception) {
            return [];
        } catch (\Magento\Framework\DB\Adapter\TableNotFoundException $exception) {
            return [];
        }
    }
    
    /**
     * Get configuration data
     * 
     * @return array
     */
    private function getConfigData()
    {
        $config = [];
        foreach ($this->getConfigValues() as $configValue) {
            $scope = $configValue->getScope();
            $scopeId = $configValue->getScopeId();
            $path = $configValue->getPath();
            $value = $configValue->getValue();
            if (empty($scope)) {
                continue;
            }
            if ($scope === \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT) {
                $config[$scope][$path] = $value;
            } else {
                $code = $this->scopeCodeResolver->resolve($scope, $scopeId);
                $config[$scope][$code][$path] = $value;
            }
        }
        foreach ($this->getSourceConfigValues() as $configValue) {
            $scope = $configValue->getScope();
            $scopeId = $configValue->getScopeId();
            $path = $configValue->getPath();
            $value = $configValue->getValue();
            $sourceCode = (string) $configValue->getSourceCode();
            if (empty($scope)) {
                continue;
            }
            if ($scope === \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT) {
                $config[$scope]['sources'][$sourceCode][$path] = $value;
            } else {
                $code = $this->scopeCodeResolver->resolve($scope, $scopeId);
                $config[$scope][$code]['sources'][$sourceCode][$path] = $value;
            }
        }
        return $config;
    }
    
    /**
     * Load configuration
     * 
     * @return array
     */
    private function loadConfig()
    {
        $config = $this->getConfigData();
        foreach ($config as $scope => &$item) {
            if ($scope === \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT) {
                $item = $this->converter->convert($item);
                if (!empty($item['sources'])) {
                    foreach ($item['sources'] as $sourceCode => &$sourceItem) {
                        $sourceItem = $this->converter->convert($sourceItem);
                    }
                }
            } else {
                foreach ($item as &$scopeItems) {
                    $scopeItems = $this->converter->convert($scopeItems);
                    if (!empty($scopeItems['sources'])) {
                        foreach ($scopeItems['sources'] as $sourceCode => &$sourceItem) {
                            $sourceItem = $this->converter->convert($sourceItem);
                        }
                    }
                }
            }
        }
        return $config;
    }
}