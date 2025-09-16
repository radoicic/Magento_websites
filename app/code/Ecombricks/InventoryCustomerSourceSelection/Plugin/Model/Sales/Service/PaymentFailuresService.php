<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Sales\Service;

/**
 * Payment failures service plugin
 */
class PaymentFailuresService extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Inline translation
     * 
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    private $inlineTranslation;
    
    /**
     * Transport builder
     * 
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;
    
    /**
     * Locale date
     * 
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;
    
    /**
     * Cart repository
     * 
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $cartRepository;
    
    /**
     * Logger
     * 
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    
    /**
     * Get source by source code
     * 
     * @var \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode
     */
    private $getSourceBySourceCode;
    
    /**
     * Shipping method full code
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Data\Quote\ShippingMethodFullCode
     */
    private $shippingMethodFullCode;
    
    /**
     * Quote
     * 
     * @var \Magento\Quote\Api\Data\CartInterface 
     */
    private $quote;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode $getSourceBySourceCode
     * @param \Ecombricks\InventoryCustomerSourceSelection\Data\Quote\ShippingMethodFullCode $shippingMethodFullCode
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Psr\Log\LoggerInterface $logger,
        \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode $getSourceBySourceCode,
        \Ecombricks\InventoryCustomerSourceSelection\Data\Quote\ShippingMethodFullCode $shippingMethodFullCode
    )
    {
        parent::__construct($wrapperFactory);
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->localeDate = $localeDate;
        $this->cartRepository = $cartRepository;
        $this->logger = $logger;
        $this->getSourceBySourceCode = $getSourceBySourceCode;
        $this->shippingMethodFullCode = $shippingMethodFullCode;
    }
    
    /**
     * Around handle
     * 
     * @param \Magento\Sales\Model\Service\PaymentFailuresService $subject
     * @param \Closure $proceed
     * @param int $cartId
     * @param string $message
     * @param string $checkoutType
     * @return \Magento\Sales\Api\PaymentFailuresInterface
     */
    public function aroundHandle(
        \Magento\Sales\Model\Service\PaymentFailuresService $subject,
        \Closure $proceed,
        int $cartId,
        string $message,
        string $checkoutType = 'onepage'
    ): \Magento\Sales\Api\PaymentFailuresInterface
    {
        $this->setSubject($subject);
        $this->inlineTranslation->suspend();
        $this->setQuoteByQuoteId($cartId);
        $template = $this->getConfigValue('checkout/payment_failed/template');
        $receiver = $this->getConfigValue('checkout/payment_failed/receiver');
        $sendTo = [
            [
                'email' => $this->getConfigValue('trans_email/ident_'.$receiver.'/email'),
                'name' => $this->getConfigValue('trans_email/ident_'.$receiver.'/name'),
            ],
        ];
        $copyMethod = $this->getConfigValue('checkout/payment_failed/copy_method');
        $copyTo = $this->invokeSubjectMethod('getConfigEmails', $this->getQuote());
        $bcc = [];
        if (!empty($copyTo)) {
            switch ($copyMethod) {
                case 'bcc':
                    $bcc = $copyTo;
                    break;
                case 'copy':
                    foreach ($copyTo as $email) {
                        $sendTo[] = ['email' => $email, 'name' => null];
                    }
                    break;
            }
        }
        foreach ($sendTo as $recipient) {
            $transport = $this->transportBuilder->setTemplateIdentifier($template)
                ->setTemplateOptions([
                    'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ])
                ->setTemplateVars($this->getTemplateVars($message, $checkoutType))
                ->setFrom($this->getConfigValue('checkout/payment_failed/identity'))
                ->addTo($recipient['email'], $recipient['name'])
                ->addBcc($bcc)
                ->getTransport();
            try {
                $transport->sendMessage();
            } catch (\Exception $exception) {
                $this->logger->critical($exception->getMessage());
            }
        }
        $this->inlineTranslation->resume();
        return $subject;
    }
    
    /**
     * Get shipping carrier description
     *
     * @return string
     */
    protected function getShippingCarrierDescription(): string
    {
        $descriptionPieces = [];
        $shippingMethods = $this->getQuote()->getShippingAddress()->getShippingMethod();
        foreach ($shippingMethods as $sourceCode => $shippingMethod) {
            $source = $this->getSourceBySourceCode->execute((string) $sourceCode);
            if (empty($source)) {
                continue;
            }
            $shippingCarrierCode = $this->shippingMethodFullCode->parse($shippingMethod)[0];
            $descriptionPieces[] = $source->getName().': '.$this->getConfigValue('carriers/'.$shippingCarrierCode.'/title');
        }
        return implode(', ', $descriptionPieces);
    }
    
    /**
     * Get configuration value
     * 
     * @param string $path
     * @return mixed
     */
    protected function getConfigValue(string $path)
    {
        return $this->invokeSubjectMethod('getConfigValue', $path, $this->getQuote());
    }
    
    /**
     * Set quote by quote ID
     * 
     * @param int $quoteId
     * @return void
     */
    private function setQuoteByQuoteId(int $quoteId): void
    {
        $this->quote = $this->cartRepository->get($quoteId);
    }
    
    /**
     * Get quote
     * 
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getQuote(): \Magento\Quote\Api\Data\CartInterface
    {
        if ($this->quote === null) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Quote is not set for the payment failures service.'));
        }
        return $this->quote;
    }
        
    /**
     * Get store ID
     * 
     * @return int
     */
    private function getStoreId(): int
    {
        return (int) $this->getQuote()->getStoreId();
    }
    
    /**
     * Get payment method description
     * 
     * @return string
     */
    private function getPaymentMethodDescription(): string
    {
        $paymentMethodCode = $this->getQuote()->getPayment()->getMethod() ?? '';
        if (empty($paymentMethodCode)) {
            return '';
        }
        return $this->getConfigValue('payment/'.$paymentMethodCode.'/title');
    }
    
    /**
     * Get template vars
     *
     * @param string $message
     * @param string $checkoutType
     * @return array
     */
    private function getTemplateVars(string $message, string $checkoutType): array
    {
        $quote = $this->getQuote();
        $billingAddress = $quote->getBillingAddress();
        $shippingAddress = $quote->getShippingAddress();
        $currencyCode = $quote->getCurrency()->getStoreCurrencyCode();
        return [
            'reason' => $message,
            'checkoutType' => $checkoutType,
            'dateAndTime' => $this->invokeSubjectMethod('getLocaleDate'),
            'customer' => $this->invokeSubjectMethod('getCustomerName', $quote),
            'customerEmail' => $billingAddress->getEmail(),
            'billingAddress' => $billingAddress,
            'shippingAddress' => $shippingAddress,
            'billingAddressHtml' => $billingAddress->format('html'),
            'shippingAddressHtml' => $shippingAddress->format('html'),
            'shippingMethod' => $this->getShippingCarrierDescription(),
            'paymentMethod' => $this->getPaymentMethodDescription(),
            'items' => implode('<br />', $this->invokeSubjectMethod('getQuoteItems', $this->getQuote())),
            'total' => $currencyCode.' '.$quote->getGrandTotal(),
        ];
    }
}