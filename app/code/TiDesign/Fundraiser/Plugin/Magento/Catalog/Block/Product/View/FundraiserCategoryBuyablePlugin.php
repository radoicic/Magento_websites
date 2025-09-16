<?php

namespace TiDesign\Fundraiser\Plugin\Magento\Catalog\Block\Product\View;

use TiDesign\Fundraiser\Helper\Config as ConfigHelper;
use TiDesign\Fundraiser\Model\Catalog\Category\IsFundraiserCategories;
use TiDesign\Fundraiser\Model\Catalog\Product\GetCategoryIdsByProduct;
use TiDesign\Fundraiser\Model\ThemeCustomer\GetThemeCustomerByTrackingCode;

class FundraiserCategoryBuyablePlugin
{
    const ADD_TO_CART_BLOCK_NAMES = [
        'product.info.addtocart',
        'product.info.addtocart.additional'
    ];

    /**
     * @param ConfigHelper $configHelper
     * @param GetCategoryIdsByProduct $getCategoryIdsByProduct
     * @param IsFundraiserCategories $isFundraiserCategories
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param GetThemeCustomerByTrackingCode $getThemeCustomerByTrackingCode
     */
    public function __construct(
        protected ConfigHelper $configHelper,
        protected GetCategoryIdsByProduct $getCategoryIdsByProduct,
        protected IsFundraiserCategories $isFundraiserCategories,
        protected \Magento\Checkout\Model\Session $checkoutSession,
        protected GetThemeCustomerByTrackingCode $getThemeCustomerByTrackingCode
    ) {
    }

    /**
     * @param \Magento\Catalog\Block\Product\View $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml($subject, $result)
    {
        if (!$this->configHelper->isEnabled()) {
            return $result;
        }
        if (in_array($subject->getNameInLayout(), self::ADD_TO_CART_BLOCK_NAMES)) {
            $categoryIds = $this->getCategoryIdsByProduct->execute($subject->getProduct());
            $isFundraiserCategories = $this->isFundraiserCategories->execute($categoryIds);
            if (!in_array(true, array_values($isFundraiserCategories))) {
                return $result;
            }
            $quote = $this->checkoutSession->getQuote();
            $trackingCode = $quote ? $quote->getData('fundraiser_tracking_code') : null;
            if (!$trackingCode) {
                return '';
            }
            $themeCustomer = $this->getThemeCustomerByTrackingCode->execute($trackingCode);
            if ($themeCustomer &&
                $themeCustomer->getId() &&
                $themeCustomer->getStatus() == \TiDesign\Fundraiser\Model\ThemeCustomer\OptionSource\Status::APPROVED &&
                array_intersect($categoryIds, $themeCustomer->getCategoryIds())) {
                return $result;
            }
            return '';
        }
        return $result;
    }
}
