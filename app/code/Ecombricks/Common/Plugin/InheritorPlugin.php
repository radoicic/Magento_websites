<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\Common\Plugin;

/**
 * Inheritor plugin
 */
class InheritorPlugin extends \Ecombricks\Common\Plugin\Plugin
{
    
    /**
     * Parent
     * 
     * @var object
     */
    protected $parent;
    
    /**
     * Parent wrapper
     * 
     * @var \Ecombricks\Common\DataObject\Wrapper 
     */
    protected $parentWrapper;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
    )
    {
        parent::__construct($wrapperFactory);
    }
    
    /**
     * Set parent
     * 
     * @param object $parent
     * @return $this
     */
    protected function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }
    
    /**
     * Get parent
     * 
     * @return object
     */
    protected function getParent()
    {
        if (empty($this->parent)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Parent is undefined for %1 plugin.', get_class($this)));
        }
        return $this->parent;
    }
    
    /**
     * Get parent wrapper
     * 
     * @return \Ecombricks\Common\DataObject\Wrapper
     */
    protected function getParentWrapper(): \Ecombricks\Common\DataObject\Wrapper
    {
        return $this->wrapperFactory->create($this->getParent());
    }
    
    /**
     * Invoke parent method
     * 
     * @param string $methodName
     * @param mixed ...$arguments
     * @return mixed
     */
    protected function invokeParentMethod(string $methodName, ...$arguments)
    {
        return $this->getParentWrapper()->invokeMethod($methodName, ...$arguments);
    }
    
    /**
     * Get parent property value
     * 
     * @param string $propertyName
     * @return mixed
     */
    protected function getParentPropertyValue(string $propertyName)
    {
        return $this->getParentWrapper()->getPropertyValue($propertyName);
    }
    
    /**
     * Set parent property value
     * 
     * @param string $propertyName
     * @param mixed $propertyValue
     * @return $this
     */
    protected function setParentPropertyValue(string $propertyName, $propertyValue)
    {
        $this->getParentWrapper()->setPropertyValue($propertyName, $propertyValue);
        return $this;
    }
    
}