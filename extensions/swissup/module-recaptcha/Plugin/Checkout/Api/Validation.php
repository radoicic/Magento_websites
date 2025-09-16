<?php

namespace Swissup\Recaptcha\Plugin\Checkout\Api;

use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\Data\AddressInterface;

class Validation extends AbstractValidation
{
    /**
     * @var string
     */
    protected $formId = 'checkout_payments_signedin';

    /**
     * @param  PaymentInformationManagementInterface $subject
     * @param  string                                $cartId
     * @param  PaymentInterface                      $paymentMethod
     * @param  AddressInterface|null                 $billingAddress
     * @return void
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        PaymentInformationManagementInterface $subject,
        $cartId,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        $this->validateCaptcha($paymentMethod);
    }

    /**
     * @param  PaymentInformationManagementInterface $subject
     * @param  string                                $cartId
     * @param  PaymentInterface                      $paymentMethod
     * @param  AddressInterface|null                 $billingAddress
     * @return void
     */
    public function beforeSavePaymentInformation(
        PaymentInformationManagementInterface $subject,
        $cartId,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        $this->validateCaptcha($paymentMethod);
    }
}
