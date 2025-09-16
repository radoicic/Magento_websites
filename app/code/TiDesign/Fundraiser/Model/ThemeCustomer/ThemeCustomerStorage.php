<?php

namespace TiDesign\Fundraiser\Model\ThemeCustomer;

class ThemeCustomerStorage
{
    protected $themeCustomerByIds = [];
    protected $themeCustomerByTrackingCodes = [];


    /**
     * @param \TiDesign\Fundraiser\Model\ThemeCustomer|null $themeCustomer
     * @return void
     */
    public function setThemeCustomer($themeCustomer)
    {
        if ($themeCustomer && $themeCustomer->getId()) {
            $this->themeCustomerByIds[$themeCustomer->getId()] = $themeCustomer;
            $this->themeCustomerByTrackingCodes[$themeCustomer->getTrackingCode()] = $themeCustomer;
        }
    }

    /**
     * @param int $id
     * @return \TiDesign\Fundraiser\Model\ThemeCustomer|null
     */
    public function getById($id)
    {
        return $this->themeCustomerByIds[$id] ?? null;
    }

    /**
     * @param string $trackingCode
     * @return \TiDesign\Fundraiser\Model\ThemeCustomer|null
     */
    public function getByTrackingCode($trackingCode)
    {
        return $this->themeCustomerByTrackingCodes[$trackingCode] ?? null;
    }
}
