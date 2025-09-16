<?php

namespace CityBeach\Integration\Model\Order;

use CityBeach\Integration\Api\Data\PaymentInterface;

class Payment implements PaymentInterface
{
    /**
     * @var string $paymentType
     */
    private $paymentType;

    /**
     * @var string $transactionId
     */
    private $transactionId;

    /**
     * @var string $transactionDate
     */
    private $transactionDate;

    /**
     * @var float $amount
     */
    private $amount;

    /**
     * @var string $currency
     */
    private $currency;

    /**
     * @var string $userReference
     */
    private $userReference;

    /**
     * @var string $transactionSource
     */
    private $transactionSource;

    /**
     * @return string
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * @param string $paymentType
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param string $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return string
     */
    public function getTransactionDate()
    {
        return $this->transactionDate;
    }

    /**
     * @param string $transactionDate
     */
    public function setTransactionDate($transactionDate)
    {
        $this->transactionDate = $transactionDate;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getUserReference()
    {
        return $this->userReference;
    }

    /**
     * @param string $userReference
     */
    public function setUserReference($userReference)
    {
        $this->userReference = $userReference;
    }

    /**
     * @return string
     */
    public function getTransactionSource()
    {
        return $this->transactionSource;
    }

    /**
     * @param string $transactionSource
     */
    public function setTransactionSource($transactionSource)
    {
        $this->transactionSource = $transactionSource;
    }


    public function validate()
    {
        if (empty($this->paymentType) ||
            empty($this->transactionId) ||
            empty($this->amount) ||
            empty($this->currency) ||
            empty($this->userReference)
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Missing required property on payment'));
        }
    }
}
