<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Api\InventorySales\Data;

/**
 * Website sales channel interface factory
 */
class WebsiteSalesChannelInterfaceFactory
{
    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Website repository
     * 
     * @var \Magento\Store\Api\WebsiteRepositoryInterface
     */
    private $websiteRepository;

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
     * @param \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository
     * @param string $instanceName
     * @return void
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository,
        $instanceName = \Magento\InventorySalesApi\Api\Data\SalesChannelInterface::class
    )
    {
        $this->objectManager = $objectManager;
        $this->websiteRepository = $websiteRepository;
        $this->instanceName = $instanceName;
    }

    /**
     * Create
     *
     * @param int $websiteId
     * @return \Magento\InventorySalesApi\Api\Data\SalesChannelInterface
     */
    public function create(int $websiteId): \Magento\InventorySalesApi\Api\Data\SalesChannelInterface
    {
        return $this->objectManager->create($this->instanceName, [
            'data' => [
                'type' => \Magento\InventorySalesApi\Api\Data\SalesChannelInterface::TYPE_WEBSITE,
                'code' => $this->websiteRepository->getById($websiteId)->getCode(),
            ]
        ]);
    }
}