<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales;

/**
 * Order wrapper
 */
class Order extends \Ecombricks\Common\DataObject\Wrapper
{
    /**
     * Wrapper factory
     * 
     * @var \Ecombricks\Common\DataObject\WrapperFactory
     */
    private $wrapperFactory;

    /**
     * Order item wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory 
     */
    private $orderItemWrapperFactory;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory $orderItemWrapperFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory,
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory $orderItemWrapperFactory
    )
    {
        parent::__construct($objectReflectionFactory);
        $this->wrapperFactory = $wrapperFactory;
        $this->orderItemWrapperFactory = $orderItemWrapperFactory;
    }

    /**
     * After load
     * 
     * @return void
     */
    public function afterLoad(): void
    {
        $this->setShippingMethods($this->getResourceShippingMethodWrapper()->getSourceOptions($this->getObject()->getId()));
    }

    /**
     * After save
     * 
     * @return void
     */
    public function afterSave(): void
    {
        $this->getResourceShippingMethodWrapper()->saveSourceOptions($this->getObject()->getId(), $this->getShippingMethods());
    }

    /**
     * Get source codes
     * 
     * @return array
     */
    public function getSourceCodes(): array
    {
        $sourceCodes = [];
        $order = $this->getObject();
        foreach ($order->getAllItems() as $item) {
            $sourceCode = (string) $this->orderItemWrapperFactory->create($item)->getSourceCode();
            if (empty($sourceCode)) {
                continue;
            }
            if (in_array($sourceCode, $sourceCodes)) {
                continue;
            }
            $sourceCodes[] = $sourceCode;
        }
        return $sourceCodes;
    }

    /**
     * Set shipping methods
     * 
     * @param array $shippingMethods
     * @return void
     */
    public function setShippingMethods(array $shippingMethods): void
    {
        $this->getShippingMethodWrapper()->setSourceOptions($shippingMethods);
    }
    
    /**
     * Get shipping methods
     * 
     * @return array
     */
    public function getShippingMethods(): array
    {
        return $this->getShippingMethodWrapper()->getSourceOptions();
    }

    /**
     * Set shipping method
     * 
     * @param string $sourceCode
     * @param string $shippingMethod
     * @return void
     */
    public function setShippingMethod(string $sourceCode, string $shippingMethod): void
    {
        $this->getShippingMethodWrapper()->setSourceOption($sourceCode, $shippingMethod);
    }
    
    /**
     * Get shipping method
     * 
     * @param string $sourceCode
     * @return string|null
     */
    public function getShippingMethod(string $sourceCode): ?string
    {
        return $this->getShippingMethodWrapper()->getSourceOption($sourceCode);
    }

    /**
     * Get resource shipping method wrapper
     * 
     * @return \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\ResourceModel\Order\ShippingMethod
     */
    private function getResourceShippingMethodWrapper(): \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\ResourceModel\Order\ShippingMethod
    {
        return $this->wrapperFactory->create(
            $this->getObject()->getResource(),
            \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\ResourceModel\Order\ShippingMethod::class
        );
    }

    /**
     * Get shipping method wrapper
     * 
     * @return \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ShippingMethod
     */
    private function getShippingMethodWrapper(): \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ShippingMethod
    {
        $wrapper = $this->wrapperFactory->create(
            $this->getObject(),
            \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ShippingMethod::class
        );
        $wrapper->setSourceCodes($this->getSourceCodes());
        return $wrapper;
    }
}