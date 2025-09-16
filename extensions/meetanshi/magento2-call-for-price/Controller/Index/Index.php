<?php

namespace Meetanshi\Callforprice\Controller\Index;

use Magento\Directory\Model\CountryFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Area;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Meetanshi\Callforprice\Helper\Data as HelperData;
use Meetanshi\Callforprice\Helper\SmsHelper;
use Meetanshi\Callforprice\Model\Callforprice;

/**
 * Class Index
 */
class Index extends Action
{

    /**
     * @var Callforprice
     */
    private $callforpriceModel;
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;
    /**
     * @var StateInterface
     */
    private $inlineTranslation;
    /**
     * @var HelperData
     */
    private $helperData;
    /**
     * @var CountryFactory
     */
    private $countryFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var SmsHelper
     */
    private $smsHelper;

    /**
     * Index constructor.
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param Callforprice $collectionFactory
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param HelperData $helperData
     * @param CountryFactory $countryFactory
     * @param StoreManagerInterface $storeManager
     * @param SmsHelper $smsHelper
     */
    public function __construct(
        Context $context,
        Callforprice $collectionFactory,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        HelperData $helperData,
        CountryFactory $countryFactory,
        StoreManagerInterface $storeManager,
        SmsHelper $smsHelper
    ) {
        $this->callforpriceModel = $collectionFactory;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->helperData = $helperData;
        $this->countryFactory = $countryFactory;
        $this->storeManager = $storeManager;
        $this->smsHelper = $smsHelper;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        if ($this->_request->getParams()) {
            $cname = $this->_request->getParam('cname');
            $email = $this->_request->getParam('email');
            $countryCode = $this->_request->getParam('country_id');
            $phoneNumber = $this->_request->getParam('phone_number');
            $comment = $this->_request->getParam('comment');
            $privacy = $this->_request->getParam('privacy');
            $callforpid = $this->_request->getParam('callProductId');

            if ($cname != "" && $email != "" && $countryCode != "" && $phoneNumber != "" && $comment != "") {
                if ((
                    $this->helperData->isEnablePrivacy() && ($privacy != "" || $privacy !== null)
                ) ||
                    !$this->helperData->isEnablePrivacy()
                ) {
                    if ($this->helperData->captchaEnable()) {
                        $token = $this->_request->getParam('g-recaptcha-response');
                        $validation = $this->helperData->validate($token);

                        if (!$validation['success']) {
                            $this->messageManager->addErrorMessage(
                                __($validation['error'] . 'Please Refresh the Page')
                            );
                            return;
                        }
                    }

                    try {
                        $resultPage = $this->callforpriceModel;
                        $resultPage->setCname(strip_tags($cname));
                        $resultPage->setEmail($email);
                        $resultPage->setCountry($countryCode);
                        $resultPage->setPhoneNumber($phoneNumber);
                        $resultPage->setComment(strip_tags($comment));
                        $resultPage->setProductid($callforpid);
                        $resultPage->save();
                        $country = $this->countryFactory->create()->loadByCode($countryCode);
                        $prdName = $this->helperData->getProdName($callforpid);
                        $templateVars = [
                            'storename' => $this->helperData->getStoreName(),
                            'cname' => $cname,
                            'email' => $email,
                            'country' => $country->getName(),
                            'phonenumber' => $phoneNumber,
                            'message' => $comment,
                            'pname' => $prdName
                        ];
                        if ($this->helperData->isEmailNotificationType()) {
                            $this->inlineTranslation->suspend();

                            $from = $this->helperData->getCallforEmailSender();
                            $customerFrom = $this->helperData->getCallforCustomerEmailSender();
                            $this->inlineTranslation->suspend();
                            $to = $this->helperData->getAdminEmail();

                            $templateOptions = [
                                'area' => Area::AREA_FRONTEND,
                                'store' => $this->storeManager->getStore()->getId()
                            ];

                            $templatePath = $this->helperData->getAdminEmailTemplate(
                                $this->storeManager->getStore()->getId()
                            );

                            $transport = $this->transportBuilder->setTemplateIdentifier($templatePath)
                                ->setTemplateOptions($templateOptions)
                                ->setTemplateVars($templateVars)
                                ->setFrom($from)
                                ->addTo($to)
                                ->getTransport();
                            $transport->sendMessage();
                            if ($this->helperData->isCustomerAutoReply()) {
                                $this->sendAutoReply($customerFrom, $email, $cname, $templateOptions, $prdName);
                            }
                            $this->inlineTranslation->resume();
                        } else {
                            $this->sendSms(
                                $this->helperData->getAdminMobileNumber(),
                                $this->helperData->getAdminMessage(),
                                $templateVars
                            );
                            if ($this->helperData->isCustomerSmsAutoReplyEnable()) {
                                $this->sendSms(
                                    $templateVars['phonenumber'],
                                    $this->helperData->getCustomerMessage(),
                                    $templateVars
                                );
                            }
                        }
                        $this->messageManager->addSuccessMessage(__('Your inquiry submitted successfully'));
                        $this->_redirect('*/*/');
                    } catch (\Exception $e) {
                        $this->inlineTranslation->resume();
                        $this->messageManager->addErrorMessage(__("We can\'t process your request" . $e->getMessage()));
                        $this->_redirect('*/*/');
                    }
                } else {
                    $this->messageManager->addErrorMessage(__("Please Enter Required Field(s)"));
                }
            } else {
                $this->messageManager->addErrorMessage(__("Please Enter Required Field(s)"));
            }
        }
    }

    /**
     * @param $from
     * @param $to
     * @param $cname
     * @param $templateOptions
     * @param $prdName
     */
    public function sendAutoReply($from, $to, $cname, $templateOptions, $prdName)
    {
        $vars = [
            'cname' => $cname,
            'productname' => $prdName
        ];
        try {
            $autoTemplatePath = $this->helperData->getAutoReplayTemplate($this->storeManager->getStore()->getId());

            $transport = $this->transportBuilder->setTemplateIdentifier($autoTemplatePath)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($vars)
                ->setFrom($from)
                ->addTo($to)
                ->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(__("We can\'t process your request" . $e->getMessage()));
            $this->_redirect('*/*/');
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
     * @return void
     */
    public function sendSms($receiverNumber, $message, $templateVars)
    {
        $message = $this->prepareSms($message, $templateVars);
        $this->smsHelper->sendSms($receiverNumber, $message);
    }
}
