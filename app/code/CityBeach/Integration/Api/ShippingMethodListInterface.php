<?php

namespace CityBeach\Integration\Api;

interface ShippingMethodListInterface
{
    /**
     * Return a list of shipping methods.
     *
     * @api
     * @return mixed
     */
    public function execute();
}
