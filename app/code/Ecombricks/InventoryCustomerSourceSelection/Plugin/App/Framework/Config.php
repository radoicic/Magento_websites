<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\App\Framework;

/**
 * Configuration plugin
 */
class Config extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Current source provider
     * 
     * @var \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface
     */
    private $currentSourceProvider;

    /**
     * Scope code resolver
     * 
     * @var \Magento\Framework\App\Config\ScopeCodeResolver
     */
    private $scopeCodeResolver;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface $currentSourceProvider
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Magento\Framework\App\Config\ScopeCodeResolver $scopeCodeResolver
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface $currentSourceProvider,
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Magento\Framework\App\Config\ScopeCodeResolver $scopeCodeResolver
    )
    {
        parent::__construct($wrapperFactory);
        $this->currentSourceProvider = $currentSourceProvider;
        $this->scopeCodeResolver = $scopeCodeResolver;
    }
    
    /**
     * Around get value
     * 
     * @param \Magento\Framework\App\Config $subject
     * @param \Closure $proceed
     * @param string $path
     * @param string $scope
     * @param null|string $scopeCode
     * @return mixed
     */
    public function aroundGetValue(
        \Magento\Framework\App\Config $subject,
        \Closure $proceed,
        $path = null,
        $scope = \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    )
    {
        $this->setSubject($subject);
        $scopePath = $this->getScopePath($scope, $scopeCode);
        $sourceValue = $this->getSourceValue($scopePath, $path);
        if (($sourceValue !== null) && !is_array($sourceValue)) {
            return $sourceValue;
        }
        $value = $subject->get('system', $this->getConfigPath($scopePath, $path));
        if (($sourceValue !== null) && is_array($sourceValue)) {
            if (is_array($value)) {
                $value = array_replace_recursive($value, $sourceValue);
            } else {
                $value = $sourceValue;
            }
        }
        return $value;
    }

    /**
     * Get scope path
     * 
     * @param string $scope
     * @param string|null $scopeCode
     * @return string
     */
    private function getScopePath($scope, $scopeCode)
    {
        if ($scope === 'store') {
            $scope = 'stores';
        } elseif ($scope === 'website') {
            $scope = 'websites';
        }
        $scopePath = $scope;
        if ($scope !== 'default') {
            if (is_numeric($scopeCode) || $scopeCode === null) {
                $scopeCode = $this->scopeCodeResolver->resolve($scope, $scopeCode);
            } else if ($scopeCode instanceof \Magento\Framework\App\ScopeInterface) {
                $scopeCode = $scopeCode->getCode();
            }
            if ($scopeCode) {
                $scopePath .= '/'.$scopeCode;
            }
        }
        return $scopePath;
    }
    
    /**
     * Get source configuration path
     * 
     * @param string $scopePath
     * @param string|null $path
     * @return string
     */
    private function getSourceConfigPath($scopePath, $path)
    {
        $sourceCode = $this->currentSourceProvider->getSourceCode();
        return $scopePath.'/sources/'.$sourceCode.(($path) ? '/'.$path : '');
    }

    /**
     * Get configuration path
     * 
     * @param string $scopePath
     * @param string|null $path
     * @return string
     */
    private function getConfigPath($scopePath, $path)
    {
        return $scopePath.(($path) ? '/'.$path : '');
    }

    /**
     * Get source value
     * 
     * @param string $scopePath
     * @param string|null $path
     * @return mixed
     */
    private function getSourceValue($scopePath, $path)
    {
        $sourceCode = $this->currentSourceProvider->getSourceCode();
        if (empty($sourceCode)) {
            return null;
        }
        return $this->getSubject()->get('system', $this->getSourceConfigPath($scopePath, $path));
    }
}