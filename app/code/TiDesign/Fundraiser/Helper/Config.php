<?php

namespace TiDesign\Fundraiser\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const FUNDRAISER_IS_ENABLED_XML_PATH = 'fundraiser/general/enabled';
    const FUNDRAISER_CUSTOMER_GROUP_XML_PATH = 'fundraiser/customer/fundraiser_customer_group';
    const FUNDRAISER_PRODUCT_SIZE_ATTRIBUTE_CODE_XML_PATH = 'fundraiser/product/product_size_attribute_code';
    const FUNDRAISER_TRACKING_CODE_PARAM_XML_PATH = 'fundraiser/tracking/tracking_code_param';

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        protected ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->scopeConfig->getValue(self::FUNDRAISER_IS_ENABLED_XML_PATH, 'websites');
    }

    /**
     * @return int|null
     */
    public function getFundraiserCustomerGroup()
    {
        return $this->scopeConfig->getValue(self::FUNDRAISER_CUSTOMER_GROUP_XML_PATH, 'websites');
    }

    /**
     * @return string
     */
    public function getProductSizeAttributeCode()
    {
        return $this->scopeConfig->getValue(self::FUNDRAISER_PRODUCT_SIZE_ATTRIBUTE_CODE_XML_PATH, 'websites');
    }

    /**
     * @return string|null
     */
    public function getTrackingCodeParam()
    {
        return $this->scopeConfig->getValue(self::FUNDRAISER_TRACKING_CODE_PARAM_XML_PATH);
    }
}
