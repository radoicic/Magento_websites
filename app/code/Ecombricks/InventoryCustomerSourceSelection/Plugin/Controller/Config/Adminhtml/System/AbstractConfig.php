<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Controller\Config\Adminhtml\System;

/**
 * Abstract configuration controller plugin
 */
class AbstractConfig
{
    /**
     * Current source provider
     * 
     * @var \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface
     */
    private $currentSourceProvider;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface $currentSourceProvider
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface $currentSourceProvider
    )
    {
        $this->currentSourceProvider = $currentSourceProvider;
    }

    /**
     * Before dispatch
     * 
     * @param \Magento\Config\Controller\Adminhtml\System\AbstractConfig $subject
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function beforeDispatch(
        \Magento\Config\Controller\Adminhtml\System\AbstractConfig $subject,
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->currentSourceProvider->setSourceCode($request->getParam('source') ?? null);
    }
}