<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\Common\DataObject;

/**
 * Object factory
 */
class Factory
{
    /**
     * Object manager
     * 
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Instance name
     * 
     * @var string
     */
    private $instanceName;

    /**
     * Constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = \Magento\Framework\DataObject::class
    )
    {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create
     *
     * @param array $data
     * @return object
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create($this->instanceName, $data);
    }
}