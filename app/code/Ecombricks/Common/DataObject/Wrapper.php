<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\Common\DataObject;

/**
 * Object wrapper
 */
class Wrapper
{
    /**
     * Object reflection factory
     * 
     * @var \Ecombricks\Common\DataObject\ReflectionFactory
     */
    private $objectReflectionFactory;

    /**
     * Object
     * 
     * @var object
     */
    private $object;

    /**
     * Object reflection
     * 
     * @var \Ecombricks\Common\DataObject\Reflection
     */
    private $objectReflection;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\ReflectionFactory $objectReflectionFactory
    )
    {
        $this->objectReflectionFactory = $objectReflectionFactory;
    }
    
    /**
     * Get object
     * 
     * @return object
     */
    public function getObject()
    {
        if (empty($this->object)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Object is undefined for %1 wrapper.', get_class($this)));
        }
        return $this->object;
    }
    
    /**
     * Set object
     * 
     * @param object $object
     * @return $this
     */
    public function setObject($object)
    {
        $this->object = $object;
        return $this;
    }

    /**
     * Invoke method
     * 
     * @param string $methodName
     * @param mixed ...$arguments
     * @return mixed
     */
    public function invokeMethod(string $methodName, ...$arguments)
    {
        return $this->getObjectReflection()->invokeMethod($this->getObject(), $methodName, ...$arguments);
    }

    /**
     * Invoke parent method
     * 
     * @param string $className
     * @param string $methodName
     * @param mixed ...$arguments
     * @return mixed
     */
    public function invokeParentMethod(string $className, string $methodName, ...$arguments)
    {
        return $this->getObjectReflection()->invokeParentMethod($this->getObject(), $className, $methodName, ...$arguments);
    }

    /**
     * Get property value
     * 
     * @param string $propertyName
     * @return mixed
     */
    public function getPropertyValue(string $propertyName)
    {
        return $this->getObjectReflection()->getPropertyValue($this->getObject(), $propertyName);
    }

    /**
     * Set property value
     * 
     * @param string $propertyName
     * @param mixed $propertyValue
     * @return $this
     */
    public function setPropertyValue(string $propertyName, $propertyValue)
    {
        $this->getObjectReflection()->setPropertyValue($this->getObject(), $propertyName, $propertyValue);
        return $this;
    }

    /**
     * Get object reflection
     * 
     * @return \Ecombricks\Common\DataObject\Reflection
     */
    private function getObjectReflection(): \Ecombricks\Common\DataObject\Reflection
    {
        if ($this->objectReflection !== null) {
            return $this->objectReflection;
        }
        return $this->objectReflection = $this->objectReflectionFactory->create(['class' => get_class($this->getObject())]);
    }
}