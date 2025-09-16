<?php

namespace TiDesign\Fundraiser\Model\Customer;

class IsCurrentCustomerEligibleAccess
{
    /**
     * @param \TiDesign\Fundraiser\Helper\Config $configHelper
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        protected \TiDesign\Fundraiser\Helper\Config $configHelper,
        protected \Magento\Customer\Model\Session $customerSession
    ) {
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function validate()
    {
        $customerGroupId = $this->customerSession->getCustomerGroupId();
        return $customerGroupId && $customerGroupId == $this->configHelper->getFundraiserCustomerGroup();
    }
}
