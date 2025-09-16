<?php

namespace Swissup\CheckoutRegistration\Plugin;

class PaymentInformationManagementForGuests
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Swissup\CheckoutRegistration\Model\RequestProcessor
     */
    private $requestProcessor;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Swissup\CheckoutRegistration\Helper\Data $helper
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Swissup\CheckoutRegistration\Model\RequestProcessor $requestProcessor
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->requestProcessor = $requestProcessor;
    }

    /**
     * @param \Magento\Checkout\Api\GuestPaymentInformationManagementInterface $subject
     * @param int $cartId
     * @param string $email
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @return void
     */
    public function beforeSavePaymentInformation(
        \Magento\Checkout\Api\GuestPaymentInformationManagementInterface $subject,
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if (!$this->checkoutSession->getQuote()->isVirtual()) {
            return;
        }

        $this->requestProcessor->processPaymentRequest($paymentMethod);
    }
}
