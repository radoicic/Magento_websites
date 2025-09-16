<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\Common\Plugin;

/**
 * Plugin
 */
class Plugin
{
    /**
     * Wrapper factory
     * 
     * @var \Ecombricks\Common\DataObject\WrapperFactory
     */
    protected $wrapperFactory;

    /**
     * Subject
     * 
     * @var object
     */
    protected $subject;

    /**
     * Subject wrapper
     * 
     * @var \Ecombricks\Common\DataObject\Wrapper
     */
    protected $subjectWrapper;
    
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
        $this->wrapperFactory = $wrapperFactory;
    }
    
    /**
     * Set subject
     * 
     * @param object $subject
     * @return $this
     */
    protected function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }
    
    /**
     * Get subject
     * 
     * @return object
     */
    protected function getSubject()
    {
        if (empty($this->subject)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Subject is undefined for %1 plugin.', get_class($this)));
        }
        return $this->subject;
    }
    
    /**
     * Get subject wrapper
     * 
     * @return \Ecombricks\Common\DataObject\Wrapper
     */
    protected function getSubjectWrapper(): \Ecombricks\Common\DataObject\Wrapper
    {
        return $this->wrapperFactory->create($this->getSubject());
    }
    
    /**
     * Invoke subject method
     * 
     * @param string $methodName
     * @param mixed ...$arguments
     * @return mixed
     */
    protected function invokeSubjectMethod(string $methodName, ...$arguments)
    {
        return $this->getSubjectWrapper()->invokeMethod($methodName, ...$arguments);
    }
    
    /**
     * Invoke subject parent method
     * 
     * @param string $className
     * @param string $methodName
     * @param mixed ...$arguments
     * @return mixed
     */
    protected function invokeSubjectParentMethod(string $className, string $methodName, ...$arguments)
    {
        return $this->getSubjectWrapper()->invokeParentMethod($className, $methodName, ...$arguments);
    }
    
    /**
     * Get subject property value
     * 
     * @param string $propertyName
     * @return mixed
     */
    protected function getSubjectPropertyValue(string $propertyName)
    {
        return $this->getSubjectWrapper()->getPropertyValue($propertyName);
    }
    
    /**
     * Set subject property value
     * 
     * @param string $propertyName
     * @param mixed $propertyValue
     * @return $this
     */
    protected function setSubjectPropertyValue(string $propertyName, $propertyValue)
    {
        $this->getSubjectWrapper()->setPropertyValue($propertyName, $propertyValue);
        return $this;
    }
}