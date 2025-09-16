<?php

namespace Meetanshi\Callforprice\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Meetanshi\Callforprice\Helper\Data;
use Meetanshi\Callforprice\Model\CallforpriceFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\App\Area;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Meetanshi\Callforprice\Helper\SmsHelper;

/**
 * Class Inquiry
 * @package Meetanshi\Callforprice\Model\Resolver
 */
class Inquiry implements ResolverInterface
{
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var CallforpriceFactory
     */
    private $callforpriceFactory;
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;
    /**
     * @var StateInterface
     */
    private $inlineTranslation;
    /**
     * @var CountryFactory
     */
    private $countryFactory;
    /**
     * @var SmsHelper
     */
    private $smsHelper;

    /**
     * Inquiry constructor.
     * @param Data $helper
     * @param CallforpriceFactory $callforpriceFactory
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param CountryFactory $countryFactory
     * @param SmsHelper $smsHelper
     */
    public function __construct(
        Data $helper,
        CallforpriceFactory $callforpriceFactory,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        CountryFactory $countryFactory,
        SmsHelper $smsHelper
    )
    {
        $this->helper = $helper;
        $this->callforpriceFactory = $callforpriceFactory;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->countryFactory = $countryFactory;
        $this->smsHelper = $smsHelper;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        try {
            if (!isset($args['input']['cname'])) {
                throw new GraphQlInputException(__('Required parameter "customer name" is missing.'));
            }
            if (!isset($args['input']['email'])) {
                throw new GraphQlInputException(__('Required parameter "email" is missing.'));
            }
            if (!isset($args['input']['country_id'])) {
                throw new GraphQlInputException(__('Required parameter "country_id" is missing.'));
            }
            if (!isset($args['input']['phone_number'])) {
                throw new GraphQlInputException(__('Required parameter "phone_number" is missing.'));
            }
            if (!isset($args['input']['comment'])) {
                throw new GraphQlInputException(__('Required parameter "comment" is missing.'));
            }
            if($this->helper->captchaEnable()){
                if (!isset($args['input']['g_recaptcha_response'])) {
                    throw new GraphQlInputException(__('Required parameter "g_recaptcha_response" is missing.'));
                }
                $token = $args['input']['g_recaptcha_response'];
                $validation = $this->helper->validate($token);
                if (!$validation['success']) {
                    throw new GraphQlInputException(__($validation['error']));
                }
            }
            if($this->helper->isEnablePrivacy()){
                if (!isset($args['input']['privacy'])) {
                    throw new GraphQlInputException(__('Required parameter "privacy" is missing.'));
                }
            }

            $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();

            $cname = $args['input']['cname'];
            $email = $args['input']['email'];
            $countryCode = $args['input']['country_id'];
            $phoneNumber = $args['input']['phone_number'];
            $comment = $args['input']['comment'];
            $callforpid = $args['input']['callProductId'];

            $resultPage = $this->callforpriceFactory->create();
            $resultPage->setCname(strip_tags($cname));
            $resultPage->setEmail($email);
            $resultPage->setCountry($countryCode);
            $resultPage->setPhoneNumber($phoneNumber);
            $resultPage->setComment(strip_tags($comment));
            $resultPage->setProductid($callforpid);
            $resultPage->save();

            $country = $this->countryFactory->create()->loadByCode($countryCode);
            $prdName = $this->helper->getProdName($callforpid);
            $templateVars = [
                'storename' => $this->helper->getStoreName(),
                'cname' => $cname,
                'email' => $email,
                'country' => $country->getName(),
                'phonenumber' => $phoneNumber,
                'message' => $comment,
                'pname' => $prdName
            ];
            if ($this->helper->isEmailNotificationType()) {
                $this->inlineTranslation->suspend();

                $from = $this->helper->getCallforEmailSender();
                $customerFrom = $this->helper->getCallforCustomerEmailSender();
                $this->inlineTranslation->suspend();
                $to = $this->helper->getAdminEmail();

                $templateOptions = [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $storeId
                ];

                $templatePath = $this->helper->getAdminEmailTemplate(
                    $storeId
                );

                $transport = $this->transportBuilder->setTemplateIdentifier($templatePath)
                    ->setTemplateOptions($templateOptions)
                    ->setTemplateVars($templateVars)
                    ->setFrom($from)
                    ->addTo($to)
                    ->getTransport();
                $transport->sendMessage();
                if ($this->helper->isCustomerAutoReply()) {
                    $this->sendAutoReply($customerFrom, $email, $cname, $templateOptions, $prdName,$storeId);
                }
                $this->inlineTranslation->resume();
            } else {
                $this->sendSms(
                    $this->helper->getAdminMobileNumber(),
                    $this->helper->getAdminMessage(),
                    $templateVars
                );
                if ($this->helper->isCustomerSmsAutoReplyEnable()) {
                    $this->sendSms(
                        $templateVars['phonenumber'],
                        $this->helper->getCustomerMessage(),
                        $templateVars
                    );
                }
            }

            return [
                'success' => true,
                'successmsg' => "Your inquiry submitted successfully",
                'errormsg' => "",
            ];
        } catch (\Exception $e) {
            return [
                'success' => true,
                'successmsg' => "",
                'errormsg' => $e->getMessage(),
            ];
        }
    }

    /**
     * @param $from
     * @param $to
     * @param $cname
     * @param $templateOptions
     * @param $prdName
     * @param $storeId
     * @throws GraphQlInputException
     */
    public function sendAutoReply($from, $to, $cname, $templateOptions, $prdName, $storeId)
    {
        $vars = [
            'cname' => $cname,
            'productname' => $prdName
        ];
        try {
            $autoTemplatePath = $this->helper->getAutoReplayTemplate($storeId);

            $transport = $this->transportBuilder->setTemplateIdentifier($autoTemplatePath)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($vars)
                ->setFrom($from)
                ->addTo($to)
                ->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            throw new GraphQlInputException(__("We can\'t process your request" . $e->getMessage()));
        }
    }

    /**
     * @param $message
     * @param $templateVars
     * @return mixed|string
     */
    public function prepareSms($message, $templateVars)
    {
        $replaceArray = [
            $templateVars['pname'],
            $templateVars['cname'],
            $templateVars['email'],
            $templateVars['country'],
            $templateVars['phonenumber'],
            $templateVars['storename'],
            $templateVars['message']
        ];
        $originalArray = [
            '{{product_name}}',
            '{{customer_name}}',
            '{{customer_mail}}',
            '{{customer_country}}',
            '{{customer_number}}',
            '{{store_name}}',
            '{{inquiry_details}}'
        ];
        $newMessage = str_replace($originalArray, $replaceArray, $message);
        $newMessage = html_entity_decode($newMessage);
        return $newMessage;
    }

    /**
     * @param $receiverNumber
     * @param $message
     * @param $templateVars
     */
    public function sendSms($receiverNumber, $message, $templateVars)
    {
        $message = $this->prepareSms($message, $templateVars);
        $this->smsHelper->sendSms($receiverNumber, $message);
    }
}
