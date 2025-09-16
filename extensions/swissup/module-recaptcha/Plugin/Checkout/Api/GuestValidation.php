<?php

namespace Swissup\Recaptcha\Plugin\Checkout\Api;

use Magento\Checkout\Api\GuestPaymentInformationManagementInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\Data\AddressInterface;

class GuestValidation extends AbstractValidation
{
    /**
     * @var string
     */
    protected $formId = 'checkout_payments_guest';

    /**
     * @param  GuestPaymentInformationManagementInterface $subject
     * @param  string                                     $cartId
     * @param  string                                     $email
     * @param  PaymentInterface                           $paymentMethod
     * @param  AddressInterface|null                      $billingAddress
     * @return void
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        GuestPaymentInformationManagementInterface $subject,
        $cartId,
        $email,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        $this->validateCaptcha($paymentMethod);
    }

    /**
     * @param  GuestPaymentInformationManagementInterface $subject
     * @param  string                                     $cartId
     * @param  string                                     $email
     * @param  PaymentInterface                           $paymentMethod
     * @param  AddressInterface|null                      $billingAddress
     * @return void
     */
    public function beforeSavePaymentInformation(
        GuestPaymentInformationManagementInterface $subject,
        $cartId,
        $email,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        $this->validateCaptcha($paymentMethod);
    }
}
