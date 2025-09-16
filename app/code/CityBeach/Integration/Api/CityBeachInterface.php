<?php

namespace CityBeach\Integration\Api;

interface CityBeachInterface
{
    /**
     * Query magento for system and debugging information.
     *
     * @api
     * @return mixed
     */
    public function execute();

    /**
     * Query magento for system available inventory sources.
     *
     * @api
     * @return mixed
     */
    public function getSourceDetails();

}
