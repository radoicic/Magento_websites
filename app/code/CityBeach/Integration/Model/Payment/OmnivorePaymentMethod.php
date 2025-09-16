<?php
/**
 * Created by PhpStorm.
 * User: will
 * Date: 18/5/17
 * Time: 3:22 PM
 */

namespace CityBeach\Integration\Model\Payment;

class OmnivorePaymentMethod extends \Magento\Payment\Model\Method\AbstractMethod
{
    const PAYMENT_METHOD_CODE = 'omnivorepayment';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_CODE;

    /**
     * @var string
     */
    protected $_infoBlockType = 'CityBeach\Integration\Block\Info\Payment';
}