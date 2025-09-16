<?php

namespace CityBeach\Integration\Block\Info;

class Payment extends \Magento\Payment\Block\Info
{
    /**
     * @var string
     */
    protected $_template = 'CityBeach_Integration::info/payment.phtml';

    /**
     * @return string
     */
    public function getPaymentType()
    {
        return $this->getInfo()->getAdditionalInformation('payment_type');
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->getInfo()->getAdditionalInformation('transaction_id');
    }

    /**
     * @return string
     */
    public function getTransactionDate()
    {
        return $this->getInfo()->getAdditionalInformation('transaction_date');
    }

    /**
     * @return string
     */
    public function getUserReference()
    {
        return $this->getInfo()->getAdditionalInformation('user_reference');
    }

    /**
     * @return string
     */
    public function getTransactionSource()
    {
        return $this->getInfo()->getAdditionalInformation('transaction_source');
    }

    /**
     * @return string
     */
    public function getMarketplace()
    {
        return $this->getInfo()->getAdditionalInformation('marketplace');
    }
}
