<?php

namespace TiDesign\Fundraiser\Model\Tracking;

class GetCustomerIdByTrackingCode
{
    protected $trackingCodeToCustomerId = [];

    /**
     * @param string $trackingCode
     * @return int
     * @todo Waiting for confirmation
     */
    public function execute($trackingCode)
    {
        return null;
        if (!$trackingCode) {
            return null;
        }
        if (isset($this->trackingCodeToCustomerId[$trackingCode])) {
            return $this->trackingCodeToCustomerId[$trackingCode];
        }
        return 29819;
    }
}
