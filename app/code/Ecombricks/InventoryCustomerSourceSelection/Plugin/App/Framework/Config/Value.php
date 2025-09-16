<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\App\Framework\Config;

/**
 * Configuration value plugin
 */
class Value extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Current source provider
     * 
     * @var \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface
     */
    private $currentSourceProvider;

    /**
     * Resource
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\Config\ResourceModel\Config\Data $resource
     */
    private $resource;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface $currentSourceProvider
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\Config\ResourceModel\Config\Data $resource
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface $currentSourceProvider,
        \Ecombricks\InventoryCustomerSourceSelection\Model\Config\ResourceModel\Config\Data $resource
    )
    {
        parent::__construct($wrapperFactory);
        $this->currentSourceProvider = $currentSourceProvider;
        $this->resource = $resource;
    }

    /**
     * Before load
     * 
     * @param \Magento\Framework\App\Config\Value $subject
     */
    public function beforeLoad(\Magento\Framework\App\Config\Value $subject)
    {
        $this->setSubject($subject);
        $this->initResource();
    }

    /**
     * Before save
     * 
     * @param \Magento\Framework\App\Config\Value $subject
     */
    public function beforeSave(\Magento\Framework\App\Config\Value $subject)
    {
        $this->setSubject($subject);
        $this->initResource();
        $sourceCode = $this->currentSourceProvider->getSourceCode();
        if (!$sourceCode) {
            return;
        }
        $subject->setSourceCode($sourceCode);
    }

    /**
     * Before delete
     * 
     * @param \Magento\Framework\App\Config\Value $subject
     */
    public function beforeDelete(\Magento\Framework\App\Config\Value $subject)
    {
        $this->setSubject($subject);
        $this->initResource();
    }

    /**
     * Initialize resource
     * 
     * @return void
     */
    private function initResource(): void
    {
        $sourceCode = $this->currentSourceProvider->getSourceCode();
        if (!$sourceCode) {
            return;
        }
        $this->setSubjectPropertyValue('_resource', $this->resource);
    }
}