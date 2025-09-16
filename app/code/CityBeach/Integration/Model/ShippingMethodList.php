<?php

namespace CityBeach\Integration\Model;

use CityBeach\Integration\Api\ShippingMethodListInterface;

use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Shipping\Model\Config;

class ShippingMethodList implements ShippingMethodListInterface
{
    /*
     * @var \Magento\Shipping\Model\ScopeConfigInterface $scopeConfig
     */
    private $scopeConfig;

    /*
     * @var \Magento\Shipping\Model\Config $shippingConfig
     */
    private $shippingConfig;


    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Config $shippingConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->shippingConfig = $shippingConfig;
    }

    /**
     * Return a list of shipping methods.
     *
     * @api
     * @return array
     */
    public function execute()
    {
        $methods = [];

        $activeCarriers = $this->shippingConfig->getActiveCarriers();

        foreach ($activeCarriers as $carrierCode => $carrierModel) {
            $options = array();
            if ($carrierMethods = $carrierModel->getAllowedMethods()) {
                $carrierTitle = $this->scopeConfig->getValue('carriers/' . $carrierCode . '/title');

                foreach ($carrierMethods as $methodCode => $method) {
                    $methods[] = array(
                        'carrierCode' => $carrierCode,
                        'carrierTitle' => $carrierTitle,
                        'methodCode' => $methodCode,
                        'methodTitle' => $method,
                    );
                }
            }
        }

        return $methods;
    }
}
