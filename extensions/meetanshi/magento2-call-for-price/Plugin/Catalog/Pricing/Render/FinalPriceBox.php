<?php

namespace Meetanshi\Callforprice\Plugin\Catalog\Pricing\Render;

use Magento\Catalog\Pricing\Render\FinalPriceBox as CatalogFinalPriceBox;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Meetanshi\Callforprice\Helper\Data as HelperData;

/**
 * Class FinalPriceBox
 */
class FinalPriceBox
{
    /**
     * @var HelperData|null
     */
    private $helper = null;
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * FinalPriceBox constructor.
     * @param HelperData $helper
     * @param Session $customerSession
     */
    public function __construct(HelperData $helper, Session $customerSession)
    {
        $this->helper = $helper;
        $this->customerSession = $customerSession;
    }

    /**
     * @param CatalogFinalPriceBox $subject
     * @param $result
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterToHtml(CatalogFinalPriceBox $subject, $result)
    {
        $productId = "<input type='hidden' class='callforid' value='" . $subject->getSaleableItem()->getId() . "'/>";
        if ($this->helper->isEnabled()) {

            if ($this->helper->isEnableFor() == 'global') {
                if (!$this->helper->isAllowCustomerGroups()) {
                    if ($subject->getPrice()->getPriceCode() == "tier_price") {
                        return '';
                    } else {
                        return $this->helper->getCallforpriceText() . $productId;
                    }
                } elseif ($this->helper->isAllowCustomerGroups()) {
                    $customerGroupIds = $this->helper->showCustomerGroups();
                    if ($customerGroupIds) {
                        if ($subject->getPrice()->getPriceCode() == "tier_price") {
                            return '';
                        } else {
                            return $this->helper->getCallforpriceText() . $productId;
                        }
                    } else {
                        return $result;
                    }
                }
            } elseif ($this->helper->isEnableFor() != 'product') {
                if ($this->helper->isAllowCategories() && !$this->helper->isAllowCustomerGroups()) {
                    $showInCategory = $this->helper->showPrdCategories($subject->getSaleableItem()->getId());
                    if ($showInCategory) {
                        if ($subject->getPrice()->getPriceCode() == "tier_price") {
                            return '';
                        } else {
                            return $this->helper->getCallforpriceText() . $productId;
                        }
                    } else {
                        return $result;
                    }
                } elseif ($this->helper->isAllowCustomerGroups() && !$this->helper->isAllowCategories()) {
                    $customerGroupIds = $this->helper->showCustomerGroups();
                    if ($customerGroupIds) {
                        if ($subject->getPrice()->getPriceCode() == "tier_price") {
                            return '';
                        } else {
                            return $this->helper->getCallforpriceText() . $productId;
                        }
                    } else {
                        return $result;
                    }
                } elseif ($this->helper->isAllowCustomerGroups() && $this->helper->isAllowCategories()) {
                    $showInCategory = $this->helper->showPrdCategories($subject->getSaleableItem()->getId());
                    $customerGroupIds = $this->helper->showCustomerGroups();
                    if ($customerGroupIds && $showInCategory) {
                        if ($subject->getPrice()->getPriceCode() == "tier_price") {
                            return '';
                        } else {
                            return $this->helper->getCallforpriceText() . $productId;
                        }
                    } else {
                        return $result;
                    }
                }
            } else {
                $productText = "<input type='hidden' class='callforid' value='" . $subject->getSaleableItem()->getId() . "'/>";
                $callForText = $this->helper->getProductText($subject->getSaleableItem()->getId());
                if ($this->helper->isAllowCustomerGroups() && $callForText) {
                    $customerGroupIds = $this->helper->showCustomerGroups();
                    if ($customerGroupIds && $callForText) {
                        if ($subject->getPrice()->getPriceCode() == "tier_price") {
                            return '';
                        } else {
                            if ($this->helper->getProductText($subject->getSaleableItem()->getId())) {
                                return $callForText . $productText;
                            } else {
                                return $result;
                            }
                        }
                    }
                } elseif (!$this->helper->isAllowCustomerGroups() && $callForText) {
                    if ($subject->getPrice()->getPriceCode() == "tier_price") {
                        return '';
                    } else {
                        if ($this->helper->getProductText($subject->getSaleableItem()->getId())) {
                            return $callForText . $productText;
                        } else {
                            return $result;
                        }
                    }
                }
                return $result;
            }
            return $result;
        } else {
            return $result;
        }
    }
}
