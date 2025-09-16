<?php

namespace Meetanshi\Callforprice\Helper;

use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Meetanshi\Callforprice\Model\Config\Source\NotificationType;

/**
 * Class Data
 */
class Data extends AbstractHelper
{
    const XML_PATH_ACTIVE = 'callforprice/general/active';
    const XML_PATH_NOTIFICATION_TYPE = 'callforprice/general/notification_type';
    const XML_PATH_ENABLEFOR = 'callforprice/general/enablefor';
    const XML_PATH_DISPLAY_TEXT = 'callforprice/general/displayed_text';
    const XML_PATH_HEADER_TEXT = 'callforprice/general/header_text';
    const XML_PATH_ALLOW_CUSTOMER_GROUP = 'callforprice/general/allow_customer_group';
    const XML_PATH_DISPLAY_CUSTOMER_GROUPS = 'callforprice/general/display_customer_group';
    const XML_PATH_DISPLAY_CATEGORIES = 'callforprice/general/display_categories';

    const XML_PATH_DISPLAY_AS = 'callforprice/general/typeas';
    const XML_PATH_DISPLAY_LABEL_TEXT = 'callforprice/general/displayed_text_label';
    const XML_PATH_DISPLAY_LABEL_COLOR = 'callforprice/general/label_color';

    const XML_PATH_PRIVACY_ENABLE = 'callforprice/privacy_setting/privacy_enable';
    const XML_PATH_PRIVACY_TEXT = 'callforprice/privacy_setting/privacy_text';
    const XML_PATH_PRIVACY_PAGE = 'callforprice/privacy_setting/show_privacy_policy';

    const XML_PATH_ADMIN_EMAIL = 'callforprice/adminemail/adminemail';
    const XML_PATH_EMAIL_SENDER = 'callforprice/adminemail/emailsender';
    const XML_PATH_EMAIL_AUTORPY = 'callforprice/customeremail/customeractive';
    const XML_PATH_CUSTOMER_EMAIL_SENDER = 'callforprice/customeremail/emailsender';

    const XML_PATH_ADMIN_SMS_ADMIN_MOBILE_NUMBER = 'callforprice/admin_sms/admin_mobile_number';
    const XML_PATH_ADMIN_SMS_SMS_FORMAT = 'callforprice/admin_sms/sms_format';
    const XML_PATH_CUSTOMER_SMS_AUTO_REPLY = 'callforprice/customer_sms/auto_reply';
    const XML_PATH_CUSTOMER_SMS_SMS_FORMAT = 'callforprice/customer_sms/sms_format';

    const XML_EMAIL_AUTO_REPLAY_TEMPLATE = 'callforprice/customeremail/emailtemplate';
    const XML_EMAIL_ADMIN_TEMPLATE = 'callforprice/adminemail/emailtemplate';

    const XML_PATH_RECAPTCHA_ENABLE = 'callforprice/recaptcha_setting/recaptcha_enable';
    const XML_PATH_SITEKEY = 'callforprice/recaptcha_setting/sitekey';
    const XML_PATH_SECRETKEY = 'callforprice/recaptcha_setting/secret_key';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var ProductFactory
     */
    protected $product;

    /**
     * Data constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param ProductFactory $productFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Registry $registry,
        StoreManagerInterface $storeManager,
        ProductFactory $productFactory
    ) {
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->product = $productFactory;
        parent::__construct($context);
    }

    /**
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function isCustomerLoggedIn()
    {
        $customerGroupId = $this->customerSession->getCustomerGroupId();
        if ($customerGroupId == Group::NOT_LOGGED_IN_ID) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function getCurrentCategoryId()
    {
        $category = $this->registry->registry('current_category');
        if ($category != null) {
            return $this->registry->registry('current_category')->getId();
        }
        return false;
    }

    /**
     * @return bool|int
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getLoginCustomerGroupId()
    {
        $customerGroupId = $this->customerSession->getCustomerGroupId();
        if (!empty($customerGroupId)) {
            return $customerGroupId;
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ACTIVE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function isEnableFor()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLEFOR, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getAutoReplayTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_EMAIL_AUTO_REPLAY_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getAdminEmailTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(self::XML_EMAIL_ADMIN_TEMPLATE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return mixed
     */
    public function getTypeAs()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DISPLAY_AS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function isCustomerAutoReply()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_EMAIL_AUTORPY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getCustomerGroups()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DISPLAY_CUSTOMER_GROUPS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isAllowCategories()
    {
        $category = $this->scopeConfig->getValue(self::XML_PATH_ENABLEFOR, ScopeInterface::SCOPE_STORE);
        if ($category == 'category') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getCallforpriceText()
    {
        $type = $this->getTypeAs();
        if ($type == 'button') {
            $callforText = __($this->scopeConfig->getValue(self::XML_PATH_DISPLAY_TEXT, ScopeInterface::SCOPE_STORE));
            return "<div class='opencallforpopup'><div class='action primary'>" . $callforText . "</div></div>";
        } else {
            $callforText = __($this->scopeConfig->getValue(
                self::XML_PATH_DISPLAY_LABEL_TEXT,
                ScopeInterface::SCOPE_STORE
            ));
            $callforTextColor = __($this->scopeConfig->getValue(
                self::XML_PATH_DISPLAY_LABEL_COLOR,
                ScopeInterface::SCOPE_STORE
            ));
            return "<div class='callforlabel'><span style='color: $callforTextColor'>" . $callforText . "</span></div>";
        }
    }

    /**
     * @param $productId
     * @return bool|string
     */
    public function getProductText($productId)
    {
        $type = $this->getTypeAs();
        $product = $this->product->create()->load($productId);
        if ($product->getEnableCallforprice()) {
            if ($type == 'button') {
                $callforText = __($product->getCallforpriceText());
                return "<div class='opencallforpopup'><div class='action primary'>" . $callforText . "</div></div>";
            } else {
                $callforText = $product->getData('callforprice_label_text');
                $callforTextColor = $this->scopeConfig->getValue(
                    self::XML_PATH_DISPLAY_LABEL_COLOR,
                    ScopeInterface::SCOPE_STORE
                );
                return "<div class='callforlabel'><span style='color: $callforTextColor'>" . $callforText . "</span></div>";
            }
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getTitleText()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_HEADER_TEXT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getAdminEmail()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ADMIN_EMAIL, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getCallforEmailSender()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getCallforCustomerEmailSender()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CUSTOMER_EMAIL_SENDER, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function isEnablePrivacy()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PRIVACY_ENABLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getPrivacyText()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PRIVACY_TEXT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getPrivacyPageUrl()
    {
        $privacyUrl = $this->_urlBuilder->getBaseUrl();
        return $privacyUrl . $this->scopeConfig->getValue(self::XML_PATH_PRIVACY_PAGE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function showCategories()
    {
        $categoryIds = explode(",", $this->getCategoryIds());
        if (in_array($this->getCurrentCategoryId(), $categoryIds)) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function showCustomerGroups()
    {
        $customerGroups = $this->getCustomerGroups();
        if ($customerGroups=='') {
            return false;
        }
        $customerGroupIds = explode(",", $customerGroups);
        if (in_array($this->getLoginCustomerGroupId(), $customerGroupIds)) {
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function isAllowCustomerGroups()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ALLOW_CUSTOMER_GROUP, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $productId
     * @return bool
     */
    public function showPrdCategories($productId)
    {
        $categoryIds = explode(",", $this->getCategoryIds());

        if ($this->getCurrentCategoryId()) {
            if (in_array($this->getCurrentCategoryId(), $categoryIds)) {
                return true;
            }
            return false;

        } else {
            $product = $this->product->create()->load($productId);
            $cats = $product->getCategoryIds();
            foreach ($cats as $catId) {
                if (in_array($catId, $categoryIds)) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getCategoryIds()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DISPLAY_CATEGORIES, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getStoreName()
    {
        return $this->scopeConfig->getValue('general/store_information/name', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $pid
     * @return bool|string
     */
    public function getProductName($pid)
    {
        $product = $this->product->create()->load($pid);
        if ($product->getEnableCallforprice()) {
            return $product->getName();
        } else {
            return false;
        }
    }

    /**
     * @param $pid
     * @return string
     */
    public function getProdName($pid)
    {
        $product = $this->product->create()->load($pid);
        return $product->getName();
    }

    /**
     * @return mixed
     */
    public function captchaEnable()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_RECAPTCHA_ENABLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getSitekey()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SITEKEY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $token
     * @return array
     */
    public function validate($token)
    {

        $verification = [
            'success' => false,
            'error' => __('The request is invalid or malformed.')
        ];

        if ($token) {

            try {
                $secret = $this->scopeConfig->getValue(self::XML_PATH_SECRETKEY, ScopeInterface::SCOPE_STORE);

                $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' .
                    $secret . '&response=' . $token . '&remoteip=' . $_SERVER["REMOTE_ADDR"];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $response = curl_exec($ch);
                $result = json_decode($response, true);

                if ($result['success']) {
                    $verification['success'] = true;
                } elseif (array_key_exists('error-codes', $result)) {
                    $verification['error'] = $this->getErrorMessage($result['error-codes'][0]);
                }
            } catch (\Exception $e) {
                $verification['error'] = __($e->getMessage());
            }
        }
        return $verification;
    }

    /**
     * @param $errorCode
     * @return Phrase|mixed
     */
    private function getErrorMessage($errorCode)
    {
        $errorCodesGoogle = [
            'missing-input-secret' => __('The secret parameter is missing.'),
            'invalid-input-secret' => __('The secret parameter is invalid or malformed.'),
            'missing-input-response' => __('The response parameter is missing.'),
            'invalid-input-response' => __('The response parameter is invalid or malformed.'),
            'bad-request' => __('The request is invalid or malformed.')
        ];

        if (array_key_exists($errorCode, $errorCodesGoogle)) {
            return $errorCodesGoogle[$errorCode];
        }
        return __('Something is wrong.');
    }

    /**
     * @return mixed
     */
    public function getAdminMobileNumber()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ADMIN_SMS_ADMIN_MOBILE_NUMBER, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getAdminMessage()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ADMIN_SMS_SMS_FORMAT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function isCustomerSmsAutoReplyEnable()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CUSTOMER_SMS_AUTO_REPLY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getCustomerMessage()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CUSTOMER_SMS_SMS_FORMAT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getNotificationType()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_NOTIFICATION_TYPE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isEmailNotificationType()
    {
        return $this->getNotificationType()==NotificationType::NOTIFICATION_TYPE_EMAIL;
    }

    /**
     * @return bool
     */
    public function isSmsNotificationType()
    {
        return $this->getNotificationType()==NotificationType::NOTIFICATION_TYPE_SMS;
    }
}
