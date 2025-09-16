<?php
/**
 * Payment model interface.
 */

namespace CityBeach\Integration\Api\Data;

use \Magento\Framework\Api\ExtensibleDataInterface;

interface PaymentInterface extends ExtensibleDataInterface
{
    /**
     * @return string
     */
    public function getPaymentType();

    /**
     * @param string $paymentType
     */
    public function setPaymentType($paymentType);

    /**
     * @return string
     */
    public function getTransactionId();

    /**
     * @param string $transactionId
     */
    public function setTransactionId($transactionId);

    /**
     * @return string
     */
    public function getTransactionDate();

    /**
     * @param string $transactionDate
     */
    public function setTransactionDate($transactionDate);

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @param float $amount
     */
    public function setAmount($amount);

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @param string $currency
     */
    public function setCurrency($currency);

    /**
     * @return string
     */
    public function getUserReference();

    /**
     * @param string $paypalPayerId
     */
    public function setUserReference($userReference);

    /**
     * @return string
     */
    public function getTransactionSource();

    /**
     * @param string $transactionSource
     */
    public function setTransactionSource($transactionSource);

    /**
     * @throws \Exception if validation of these object fails.
     */
    public function validate();
}
