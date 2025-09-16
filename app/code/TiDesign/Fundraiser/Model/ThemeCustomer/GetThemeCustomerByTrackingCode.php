<?php

namespace TiDesign\Fundraiser\Model\ThemeCustomer;

use TiDesign\Fundraiser\Model\ThemeCustomerFactory;
use TiDesign\Fundraiser\Model\ResourceModel\ThemeCustomer as ThemeCustomerResource;

class GetThemeCustomerByTrackingCode
{

    /**
     * @param ThemeCustomerFactory $themeCustomerFactory
     * @param ThemeCustomerResource $themeCustomerResource
     * @param ThemeCustomerStorage $themeCustomerStorage
     */
    public function __construct(
        protected ThemeCustomerFactory $themeCustomerFactory,
        protected ThemeCustomerResource $themeCustomerResource,
        protected ThemeCustomerStorage $themeCustomerStorage
    ) {
    }

    /**
     * @param string $trackingCode
     * @return \TiDesign\Fundraiser\Model\ThemeCustomer|null
     */
    public function execute($trackingCode)
    {
        if (!$trackingCode) {
            return null;
        }
        $themeCustomer = $this->themeCustomerStorage->getByTrackingCode($trackingCode);
        if ($themeCustomer && $themeCustomer->getId()) {
            return $themeCustomer;
        }
        $themeCustomer = $this->themeCustomerFactory->create();
        $this->themeCustomerResource->load($themeCustomer, $trackingCode, 'tracking_code');
        if ($themeCustomer && $themeCustomer->getId()) {
            $this->themeCustomerStorage->setThemeCustomer($themeCustomer);
            return $themeCustomer;
        }
        return null;
    }
}
