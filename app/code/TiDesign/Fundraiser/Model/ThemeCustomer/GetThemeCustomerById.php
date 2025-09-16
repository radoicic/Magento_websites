<?php

namespace TiDesign\Fundraiser\Model\ThemeCustomer;

use TiDesign\Fundraiser\Model\ThemeCustomerFactory;
use TiDesign\Fundraiser\Model\ResourceModel\ThemeCustomer as ThemeCustomerResource;

class GetThemeCustomerById
{
    /**
     * @param ThemeCustomerFactory $themeCustomerFactory
     * @param ThemeCustomerResource $themeCustomerResource
     */
    public function __construct(
        protected ThemeCustomerFactory $themeCustomerFactory,
        protected ThemeCustomerResource $themeCustomerResource
    ) {
    }

    /**
     * @param int $id
     * @return \TiDesign\Fundraiser\Model\ThemeCustomer
     */
    public function execute($id)
    {
        $theme = $this->themeCustomerFactory->create();
        $this->themeCustomerResource->load($theme, $id);
        return $theme;
    }
}
