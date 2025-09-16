<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryNzboxer\Plugin\Model\Sales;

/**
 * Order model plugin
 */
class Order extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Magento\Framework\App\State $appState
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Magento\Framework\App\State $appState
    )
    {
        parent::__construct($wrapperFactory);
        $this->appState = $appState;
    }
    
    /**
     * Around get shipping method
     * 
     * @param \Magento\Sales\Model\Order $subject
     * @param \Closure $proceed
     * @param bool $asObject
     * @return array|\Magento\Framework\DataObject
     */
    public function aroundGetShippingMethod(
        \Magento\Sales\Model\Order $subject,
        \Closure $proceed,
        $asObject = false
    )
    {
        $this->setSubject($subject);
        try {
            $areaCode = $this->appState->getAreaCode();
        } catch (\Exception $exception) {
            $areaCode = null;
        }
        $isSinleShippingMethod = \in_array($areaCode, [
            \Magento\Framework\App\Area::AREA_WEBAPI_REST,
            \Magento\Framework\App\Area::AREA_WEBAPI_SOAP,
            \Magento\Framework\App\Area::AREA_GRAPHQL,
        ]);
        $shippingMethods = $proceed($asObject);
        if (!$isSinleShippingMethod || $asObject) {
            return $shippingMethods;
        }
        return \implode(',', $shippingMethods);
    }
}