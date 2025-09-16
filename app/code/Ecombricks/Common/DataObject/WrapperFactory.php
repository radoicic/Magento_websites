<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\Common\DataObject;

/**
 * Object wrapper factory
 */
class WrapperFactory
{
    /**
     * Object manager
     * 
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Wrappers
     * 
     * @var array
     */
    private $wrappers = [];

    /**
     * Constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create
     * 
     * @param object $object
     * @param string $instanceName
     * @return object
     */
    public function create($object, string $instanceName = \Ecombricks\Common\DataObject\Wrapper::class)
    {
        $objectId = spl_object_id($object);
        if (array_key_exists($objectId, $this->wrappers) && array_key_exists($instanceName, $this->wrappers[$objectId])) {
            return $this->wrappers[$objectId][$instanceName];
        }
        return $this->wrappers[$objectId][$instanceName] = $this->objectManager->create($instanceName)->setObject($object);
    }

    /**
     * Clear
     * 
     * @return void
     */
    public function clear(): void
    {
        $this->wrappers = [];
    }
}