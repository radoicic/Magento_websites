<?php

namespace Meetanshi\Callforprice\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use Twilio\Rest\ClientFactory as TwilioClientFactory;

/**
 * Class SmsHelper
 */
class SmsHelper extends AbstractHelper
{
    /**
     * @var TwilioClientFactory
     */
    private $twilioClientFactory;
    /**
     * @var ManagerInterface
     */
    private $messageManager;
    /**
     * @var Data
     */
    private $helper;

    /**
     * SmsHelper constructor.
     * @param Data $helper
     * @param ManagerInterface $messageManager
     * @param TwilioClientFactory $twilioClientFactory
     * @param Context $context
     */
    public function __construct(
        Data $helper,
        ManagerInterface $messageManager,
        TwilioClientFactory $twilioClientFactory,
        Context $context
    ) {
        parent::__construct($context);
        $this->twilioClientFactory = $twilioClientFactory;
        $this->messageManager = $messageManager;
        $this->helper = $helper;
    }

    const APIKEY = 'callforprice/apisetting/apikey';
    const SENDER = 'callforprice/apisetting/senderid';
    const APIURL = 'callforprice/apisetting/apiurl';
    const MESSAGETYPE = 'callforprice/apisetting/messagetype';
    const SID = 'callforprice/apisetting/sid';
    const TOKEN = 'callforprice/apisetting/token';
    const FROMMOBILENUMBER = 'callforprice/apisetting/frommobilenumber';
    const SMS_URL = 'callforprice/apisetting/customurl';
    const XML_PATH_API_PROVIDER = 'callforprice/apisetting/apiprovider';
    const TWILIO_MESSAGE_TYPE = 'callforprice/apisetting/sms_type';

    /**
     * @return mixed
     */
    public function getApiprovider()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_PROVIDER, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getConfig($value)
    {
        return $this->scopeConfig->getValue($value, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getTwilioSmsType()
    {
        return $this->getConfig(self::TWILIO_MESSAGE_TYPE);
    }

    /**
     * @param $receiverMobileNumber
     * @param $message
     * @return void
     */
    public function sendSms($receiverMobileNumber, $message)
    {
        try {
            if ($this->helper->isEnabled()) {
                if ($this->getApiprovider() == "msg91") {
                    $receiverMobileNumber=trim($receiverMobileNumber, '+');
                    $msg = urlencode($message);
                    $msg = urlencode($msg);
                    $apikey = $this->getConfig(self::APIKEY);
                    $senderid = $this->getConfig(self::SENDER);
                    $url = $this->getConfig(self::APIURL);
                    $msgtype = $this->getConfig(self::MESSAGETYPE);
                    $postUrl = $url . "?sender=" . $senderid . "&route=" . $msgtype . "&mobiles=" . $receiverMobileNumber . "&authkey=" . $apikey . "&message=" . $msg . "&unicode=1";
                    $curl = curl_init();
                    curl_setopt_array(
                        $curl,
                        [
                            CURLOPT_URL => $postUrl,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "GET",
                            CURLOPT_SSL_VERIFYHOST => 0,
                            CURLOPT_SSL_VERIFYPEER => 0,
                        ]
                    );
                    curl_exec($curl);
                    $err = curl_error($curl);
                    curl_close($curl);
                    if ($err) {
                        ObjectManager::getInstance()->get(LoggerInterface::class)->info("cURL Error #:" . $err);
                    }
                } elseif ($this->getApiprovider() == "textlocal") {
                    $receiverMobileNumber=trim($receiverMobileNumber, '+');
                    $url = $this->getConfig(self::APIURL);
                    $apiKey = urlencode($this->getConfig(self::APIKEY));
                    $numbers = [$receiverMobileNumber];
                    $sender = urlencode($this->getConfig(self::SENDER));
                    $message = rawurlencode($message);
                    $numbers = implode(',', $numbers);
                    $data = ['apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message];

                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    curl_close($ch);

                    $responseArray = json_decode($response, true);
                    if (!$responseArray['status'] == "success") {
                        ObjectManager::getInstance()->get(LoggerInterface::class)->info(print_r($responseArray, true));
                    }
                } elseif ($this->getApiprovider() == "twilio" || $this->getApiprovider() == "twilio_whatsapp") {
                    $sid = $this->getConfig(self::SID);
                    $token = $this->getConfig(self::TOKEN);
                    $fromMobile = $this->getConfig(self::FROMMOBILENUMBER);
                    $twilio = $this->twilioClientFactory->create([
                        'username' => $sid,
                        'password' => $token
                    ]);
                    if ($this->getApiprovider() == "twilio_whatsapp") {
                        $receiverMobileNumber = 'whatsapp:' . $receiverMobileNumber;
                        $fromMobile = 'whatsapp:' . $fromMobile;
                        $message = str_replace("\r\n", "\n", $message);
                    }
                    $message = $twilio->messages
                        ->create(
                            $receiverMobileNumber,
                            [
                                "body" => $message,
                                "from" => $fromMobile
                            ]
                        );

                    if (!$message->sid) {
                        ObjectManager::getInstance()->get(LoggerInterface::class)->info(print_r($message, true));
                    }
                } elseif ($this->getApiprovider() == 'other') {
                    $customUrl = $this->scopeConfig->getValue(self::SMS_URL, ScopeInterface::SCOPE_STORE);
                    $msg = urlencode($message);
                    $codes = ['{mobile}', '{msg}'];
                    $accurate = [$receiverMobileNumber, $msg];
                    $postUrl = str_replace($codes, $accurate, $customUrl);

                    $curl = curl_init();

                    curl_setopt_array($curl, [
                        CURLOPT_URL => $postUrl,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_SSL_VERIFYHOST => 0,
                        CURLOPT_SSL_VERIFYPEER => 0,
                    ]);

                    curl_exec($curl);

                    curl_close($curl);
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage("Message Send Error" . $e->getMessage());
        }
    }
}
