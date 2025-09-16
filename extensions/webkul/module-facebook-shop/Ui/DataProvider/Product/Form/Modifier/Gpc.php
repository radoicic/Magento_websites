<?php
 /**
  * Webkul Software
  *
  * @category Webkul
  * @package Webkul_FacebookShop
  * @author Webkul
  * @copyright Copyright (c)Webkul Software Private Limited (https://webkul.com)
  * @license https://store.webkul.com/license.html
  */
namespace Webkul\FacebookShop\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
 
class Gpc extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @param ArrayManager $arrayManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ResourceModel\Product\ActionFactory $productAction
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Webkul\FacebookShop\Helper\Data $fbShopHelper
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        ArrayManager $arrayManager,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\ActionFactory $productAction,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Webkul\FacebookShop\Helper\Data $fbShopHelper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->arrayManager = $arrayManager;
        $this->_registry = $registry;
        $this->_productAction = $productAction;
        $this->_storeManager = $storeManager;
        $this->fbShopHelper = $fbShopHelper;
        $this->request = $request;
    }

    /**
     * modify meta
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * modify data
     *
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        try {
            $postParams = $this->request->getParams();
            $defaultGpc = $this->fbShopHelper->getConfigValue('facebook_shop_product_configuration', 'default_gpc');
            $defaultBrand = $this->fbShopHelper->getConfigValue('facebook_shop_product_configuration', 'default_brand');
            if (!empty($postParams['id'])) {
                if (empty($data[$postParams['id']]['product']['google_product_category'])) {
                    $data[$postParams['id']]['product']['google_product_category'] = $defaultGpc;
                }
                if (empty($data[$postParams['id']]['product']['fb_product_brand'])) {
                    $data[$postParams['id']]['product']['fb_product_brand'] = $defaultBrand;
                }
            }
            if (!empty($data[""]['product'])) {
                $data[""]['product']['google_product_category'] = $defaultGpc;
                $data[""]['product']['fb_product_brand'] = $defaultBrand;
            }
        } catch (\Exception $e) {
            return $data;
        }
        return $data;
    }
}
