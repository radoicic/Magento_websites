<?php

namespace Meetanshi\Callforprice\Plugin\Catalog\Model;

use Magento\Catalog\Model\Product as CatalogProduct;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Meetanshi\Callforprice\Helper\Data as HelperData;

/**
 * Class Product
 */
class Product
{
    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * Product constructor.
     * @param HelperData $helper
     */
    public function __construct(HelperData $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param CatalogProduct $product
     * @param $result
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterIsSaleable(CatalogProduct $product, $result)
    {
        if ($this->helper->isEnabled()) {
            if ($this->helper->isEnableFor() == 'global') {
                if (!$this->helper->isAllowCustomerGroups()) {
                    return [];
                } elseif ($this->helper->isAllowCustomerGroups()) {
                    $customerGroupIds = $this->helper->showCustomerGroups();
                    if ($customerGroupIds) {
                        return [];
                    } else {
                        return $result;
                    }
                }
            } elseif ($this->helper->isEnableFor() != 'product') {
                if ($this->helper->isAllowCategories() && !$this->helper->isAllowCustomerGroups()) {
                    $showInCategory = $this->helper->showPrdCategories($product->getId());
                    if ($showInCategory) {
                        return [];
                    } else {
                        return $result;
                    }
                } elseif ($this->helper->isAllowCustomerGroups() && !$this->helper->isAllowCategories()) {
                    $customerGroupIds = $this->helper->showCustomerGroups();
                    if ($customerGroupIds) {
                        return [];
                    } else {
                        return $result;
                    }
                } elseif ($this->helper->isAllowCustomerGroups() && $this->helper->isAllowCategories()) {
                    $showInCategory = $this->helper->showPrdCategories($product->getId());
                    $customerGroupIds = $this->helper->showCustomerGroups();
                    if ($customerGroupIds && $showInCategory) {
                        return [];
                    } else {
                        return $result;
                    }
                }
                return $result;
            } else {
                $callForText = $this->helper->getProductText($product->getId());
                if ($this->helper->isAllowCustomerGroups() && $callForText) {
                    $customerGroupIds = $this->helper->showCustomerGroups();
                    if ($customerGroupIds && $callForText) {
                        return [];
                    } else {
                        return $result;
                    }
                } elseif (!$this->helper->isAllowCustomerGroups() && $callForText) {
                    if ($this->helper->getProductText($product->getId())) {
                        return [];
                    } else {
                        return $result;
                    }
                } else {
                    return $result;
                }
            }
        } else {
            return $result;
        }
    }
}
